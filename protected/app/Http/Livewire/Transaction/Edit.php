<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    public $transactionId;
    public $products = [];
    public $customerList = [];
    public $searchTerm = '';
    public $cart = [];
    public $transactionCode = '';
    public $transactionNote = '';
    public $transactionStatus = 'pending';
    public $selectedCustomer = '';
    public $transactionDate = '';
    public $transactionDateOld = '';

    public function mount($transactionId)
    {
        $this->customerList = User::whereHas('role', fn($q) => $q->whereIn('name', ['customer', 'reseller']))
            ->orderBy('name')
            ->get()
            ->toArray();

        $this->transactionId = $transactionId;

        $transaction = Transaction::with(['items.variant.product', 'user'])
            ->findOrFail($transactionId);

        $this->transactionCode = $transaction->transaction_code;
        $this->transactionNote = $transaction->note;
        $this->transactionStatus = $transaction->status;
        $this->selectedCustomer = $transaction->user_id;
        $this->transactionDate = $transaction->created_at->format('Y-m-d');
        $this->transactionDateOld = $transaction->created_at->format('Y-m-d');

        $customer = User::with(['role'])->findOrFail($transaction->user_id);

        foreach ($transaction->items as $item) {
            $this->cart[] = [
                'id' => $item->variant?->product_id,
                'nama' => $item->variant?->product->nama,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'oldQuantity' => $item->quantity,
                'variants' => $item->variant?->product->variants->map(function ($variant) use ($customer) {
                    $price = $customer->role->name === 'reseller' ? $variant->product->harga_jual : $variant->price;
                    $price = $price == 0 ? $variant->price : $price;

                    return [
                        'id' => $variant->id,
                        'attributes' => $variant->attributeValues->mapWithKeys(function ($attr) {
                            return [$attr->attribute->name => $attr->value];
                        })->toArray(),
                        'price' => $price,
                        'stock' => $variant->stock,
                        'sku' => $variant->sku,
                    ];
                })->toArray(),
                'variantAttributes' => $item->variant?->attributeValues->mapWithKeys(function ($attr) {
                    return [$attr->attribute->name => $attr->value];
                })->toArray(),
                'selectedVariant' => $item->variant_id,
            ];
        }
    }

    public function updatedSelectedCustomer($value)
    {
        // todo update cart using customer role
        $role = User::find($value)->role->name;

        if ($role && count($this->cart)) {
            foreach ($this->cart as $index => $cart) {
                $price = $cart['price'];

                if ($role === 'reseller') {
                    $price = Product::whereHas('variants', fn($q) => $q->where('id', $cart['selectedVariant']))
                        ->value('harga_jual');

                    $price = $price == 0 ? $cart['price'] : $price;
                } else {
                    $price = ProductVariant::find($cart['selectedVariant'])->price ?? $price;
                }

                // Update the cart price correctly
                $this->cart[$index]['price'] = $price;
            }
        }
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if ($product) {
            // Check if product already exists in the cart
            $existingItemKey = collect($this->cart)->search(function ($item) use ($productId) {
                return $item['id'] === $productId;
            });

            if ($existingItemKey !== false) {
                // Increment quantity if already in cart
                $this->cart[$existingItemKey]['quantity']++;
            } else {
                // Add new product to cart
                $this->cart[] = [
                    'id' => $product['id'],
                    'nama' => $product['nama'],
                    'price' => $product['harga_jual'],
                    'quantity' => 1,
                    'variants' => $product['variants'],
                    'selectedVariant' => $product['variants']->first()['id'] ?? null,
                ];
            }
        }
    }

    public function updateCartItem($index, $field, $value)
    {
        // Dynamically update cart item fields
        if (isset($this->cart[$index][$field])) {
            $this->cart[$index][$field] = $value;

            // If variant is updated, adjust price based on selected variant
            if ($field === 'selectedVariant') {
                $selectedVariant = collect($this->cart[$index]['variants'])
                    ->firstWhere('id', $value);

                if ($selectedVariant) {
                    $this->cart[$index]['price'] = $selectedVariant['price'];
                }
            }
        }
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart); // Reindex array
    }

    public function calculateTotal()
    {
        return array_reduce($this->cart, function ($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function render()
    {
        return view('livewire.transaction.edit');
    }

    public function searchProduct($searchTerm)
    {
        $product = Product::with(['supplier', 'variants.attributeValues.attribute'])
            ->where('nama', 'like', '%' . $searchTerm . '%')
            ->get();

        $customer = User::with(['role'])->findOrFail($this->selectedCustomer);

        return $product->map(function ($product) use ($customer) {
            return [
                'id' => $product->id,
                'nama' => $product->nama,
                'harga_jual' => $product->harga_jual,
                'supplier' => [
                    'id' => $product->supplier['id'],
                    'name' => $product->supplier['name'],
                ],
                'variants' => $product->variants->map(function ($variant) use ($customer, $product) {
                    $price = $customer->role->name === 'reseller' ? $product->harga_jual : $variant->price;
                    $price = $price == 0 ? $variant->price : $price;

                    return [
                        'id' => $variant->id,
                        'attributes' => $variant->attributeValues->mapWithKeys(function ($attr) {
                            return [$attr->attribute->name => $attr->value];
                        })->toArray(),
                        'price' => $price,
                        'stock' => $variant->stock,
                        'sku' => $variant->sku,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    public function getTransactionType()
    {
        $customer = User::find($this->selectedCustomer);
        if (strtolower($customer->name) == 'offline') {
            return 'offline';
        }

        return 'online';
    }

    public function saveCart()
    {
        DB::beginTransaction();

        try {
            $transactionDate = $this->transactionDate ? Carbon::parse($this->transactionDate) : now();

            $transaction = Transaction::find($this->transactionId);

            if ($transaction) {
                // Step 1: Restore previous stock before updating
                foreach ($transaction->items as $item) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->stock += $item->quantity; // Restore stock
                        $variant->save();
                    }
                }
                // Step 2: Update transaction
                $transaction->timestamps = false; // Disable timestamps temporarily

                $transaction->user_id = $this->selectedCustomer;
                $transaction->transaction_code = $this->transactionCode;
                $transaction->note = $this->transactionNote;
                $transaction->status = $this->transactionStatus; // pending, paid, return, cancel
                $transaction->type = $this->getTransactionType();
                $transaction->total_price = $this->calculateTotal();

                if (Carbon::parse($this->transactionDateOld)->diffInDays($transactionDate) > 0) {
                    $transaction->created_at = $transactionDate;
                }

                $transaction->save();

                $transaction->timestamps = true; // Re-enable timestamps

                // Step 3: Delete old items and insert updated cart items
                $transaction->items()->delete();

                $transaction->items()->createMany(
                    collect($this->cart)->map(function ($item) {
                        return [
                            'variant_id' => $item['selectedVariant'],
                            'quantity' => $item['quantity'],
                            'price' => (float) $item['price'],
                        ];
                    })->toArray()
                );

                // Step 4: Deduct stock based on updated cart
                foreach ($this->cart as $item) {
                    $variant = ProductVariant::find($item['selectedVariant']);
                    if ($variant) {
                        if ($variant->stock - $item['quantity'] < 0) {
                            DB::rollBack();
                            return [
                                'status' => false,
                                'message' => "Stock for variant {$variant->sku} is not enough.",
                            ];
                        }
                        $variant->stock -= $item['quantity']; // Deduct new stock
                        $variant->save();
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'message' => 'Transaction updated successfully.',
            ];
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());
            DB::rollBack();
            return $th->getMessage();
        }
    }
}
