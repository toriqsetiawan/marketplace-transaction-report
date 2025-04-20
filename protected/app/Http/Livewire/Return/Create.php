<?php

namespace App\Http\Livewire\Return;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReturnTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public $products = [];
    public $customerList = [];
    public $searchTerm = '';
    public $cart = [];
    public $transactionCode = '';
    public $transactionNote = '';
    public $selectedCustomer = '';
    public $transactionDate = '';
    public $isTransactionCode = false;

    public function mount()
    {
        $this->customerList = User::whereHas('role', fn($q) => $q->whereIn('name', ['customer', 'reseller']))
            ->orderBy('name')
            ->get()
            ->toArray();

        $this->transactionDate = date('Y-m-d');
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
        return view('livewire.return.create');
    }

    public function searchProduct($searchTerm)
    {
        $product = Product::with(['supplier', 'variants.attributeValues.attribute'])
            ->where('nama', 'like', '%' . $searchTerm . '%')
            ->get();

        if ($product->count()) {
            $customer = User::with(['role'])->findOrFail($this->selectedCustomer);

            $this->isTransactionCode = false;

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
        } else {
            $transaction = Transaction::with(['items.variant.product', 'user'])
                ->where('transaction_code', $searchTerm)
                ->first();

            if ($transaction) {
                $this->isTransactionCode = true;
                $this->transactionCode = $transaction->transaction_code;

                $customer = User::with(['role'])->findOrFail($transaction->user_id);

                $items = $transaction->items->map(function ($item) use ($customer) {
                    return [
                        'id' => $item->variant?->product_id,
                        'nama' => $item->variant?->product->nama,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'supplier' => [
                            'id' => $item->variant?->product->supplier['id'],
                            'name' => $item->variant?->product->supplier['name'],
                        ],
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
                })->toArray();

                return [
                    [
                        'id' => $transaction->id,
                        'user' => $transaction->user,
                        'transaction_code' => $transaction->transaction_code,
                        'note' => $transaction->note,
                        'transaction_date' => $transaction->created_at->format('Y-m-d'),
                        'note' => $transaction->note,
                        'total_price' => $transaction->total_price,
                        'items' => $items,
                    ]
                ];
            }
        }
    }

    public function saveCart()
    {
        try {
            DB::beginTransaction();

            $transactionDate = $this->transactionDate ? Carbon::parse($this->transactionDate) : now();

            // Save cart data to the database
            $transaction = ReturnTransaction::create([
                'user_id' => $this->selectedCustomer,
                'note' => $this->transactionNote,
                'total_price' => $this->calculateTotal(),
                'return_date' => $transactionDate,
            ]);

            // Save cart items to the database
            foreach ($this->cart as $item) {
                $transaction->items()->create([
                    'variant_id' => $item['selectedVariant'],
                    'quantity' => $item['quantity'],
                    'price' => (float) $item['price'],
                ]);

                // Update variant stock
                $variant = ProductVariant::find($item['selectedVariant']);
                $variant->stock += $item['quantity'];
                $variant->save();
            }

            // update transaction status
            $masterTrx = Transaction::where('transaction_code', $this->transactionCode)->first();

            if ($masterTrx) {
                $masterTrx->status = 'return';
                $masterTrx->save();
            }

            DB::commit();

            return [
                'status' => true,
                'message' => 'Cart saved successfully!',
            ];
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());
            DB::rollBack();
            return $th->getMessage();
        }
    }
}
