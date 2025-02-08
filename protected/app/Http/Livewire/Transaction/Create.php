<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Product;
use App\Models\ProductVariant;
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
    public $transactionStatus = 'pending';
    public $selectedCustomer = '';
    public $transactionDate = '';

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
        return view('livewire.transaction.create');
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

    public function generateTransactionCode()
    {
        if ($this->transactionCode) {
            return $this->transactionCode;
        }

        return 'TRX' . date('dmY') . mt_rand(10000, 99999);
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
        try {
            DB::beginTransaction();

            $transactionDate = $this->transactionDate ? Carbon::parse($this->transactionDate) : now();

            // Save cart data to the database
            $transaction = Transaction::create([
                'user_id' => $this->selectedCustomer,
                'transaction_code' => $this->generateTransactionCode(),
                'note' => $this->transactionNote,
                'status' => $this->transactionStatus, // pending, paid, return, cancel
                'type' => $this->getTransactionType(),
                'total_price' => $this->calculateTotal(),
                'created_at' => $transactionDate,
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

                if ($variant->stock - $item['quantity'] < 0) {
                    DB::rollBack();
                    return [
                        'status' => false,
                        'message' => "Stock for variant {$variant->sku} is not enough.",
                    ];
                }

                $variant->stock -= $item['quantity'];
                $variant->save();
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
