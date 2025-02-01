<?php

namespace App\Http\Livewire\Purchase;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public $products = [];
    public $searchTerm = '';
    public $cart = [];
    public $purchaseCode = '';
    public $purchaseNote = '';
    public $purchaseStatus = 'pending';
    public $purchaseDate = '';

    public function mount()
    {
        $this->purchaseDate = date('Y-m-d');
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
                    'price' => $product['harga_beli'],
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
        return view('livewire.purchase.create');
    }

    public function searchProduct($searchTerm)
    {
        $product = Product::with(['supplier', 'variants.attributeValues.attribute'])
            ->where('nama', 'like', '%' . $searchTerm . '%')
            ->get();

        return $product->map(function ($product) {
            return [
                'id' => $product->id,
                'nama' => $product->nama,
                'harga_beli' => $product->harga_beli,
                'supplier' => [
                    'id' => $product->supplier['id'],
                    'name' => $product->supplier['name'],
                ],
                'variants' => $product->variants->map(function ($variant) use ($product) {
                    return [
                        'id' => $variant->id,
                        'attributes' => $variant->attributeValues->mapWithKeys(function ($attr) {
                            return [$attr->attribute->name => $attr->value];
                        })->toArray(),
                        'price' => $product->harga_beli,
                        'stock' => $variant->stock,
                        'sku' => $variant->sku,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    public function generatePurchaseCode()
    {
        if ($this->purchaseCode) {
            return $this->purchaseCode;
        }

        return 'PRC' . date('dmY') . mt_rand(10000, 99999);
    }

    public function saveCart()
    {
        try {
            DB::beginTransaction();

            $purchaseDate = $this->purchaseDate ? Carbon::parse($this->purchaseDate) : now();

            // Save cart data to the database
            $purchase = Purchase::create([
                'user_id' => auth()->id(),
                'purchase_code' => $this->generatePurchaseCode(),
                'purchase_date' => $purchaseDate,
                'total_price' => $this->calculateTotal(),
                'status' => $this->purchaseStatus, // pending, completed, canceled
                'note' => $this->purchaseNote,
            ]);

            // Save cart items to the database
            foreach ($this->cart as $item) {
                $purchase->items()->create([
                    'variant_id' => $item['selectedVariant'],
                    'quantity' => $item['quantity'],
                    'price' => (float) $item['price'],
                ]);

                // Update variant stock
                $variant = ProductVariant::find($item['selectedVariant']);
                $variant->stock += $item['quantity']; // Increase stock
                $variant->save();
            }

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());
            DB::rollBack();
            return $th->getMessage();
        }
    }
}
