<?php

namespace App\Http\Livewire\Purchase;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    public $purchaseId;
    public $products = [];
    public $searchTerm = '';
    public $cart = [];
    public $purchaseNote = '';
    public $purchaseStatus = 'pending';
    public $purchaseDate = '';

    public function mount($purchaseId)
    {
        $this->purchaseId = $purchaseId;

        $purchase = Purchase::with(['items.variant.product', 'user'])
            ->findOrFail($purchaseId);

        $this->purchaseNote = $purchase->note;
        $this->purchaseStatus = $purchase->status;
        $this->purchaseDate = $purchase->purchase_date->format('Y-m-d');

        foreach ($purchase->items as $item) {
            $this->cart[] = [
                'id' => $item->variant->product_id,
                'nama' => $item->variant->product->nama,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'variants' => $item->variant->product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'attributes' => $variant->attributeValues->mapWithKeys(function ($attr) {
                            return [$attr->attribute->name => $attr->value];
                        })->toArray(),
                        'price' => $variant->product->harga_beli,
                        'stock' => $variant->stock,
                        'sku' => $variant->sku,
                    ];
                })->toArray(),
                'variantAttributes' => $item->variant->attributeValues->mapWithKeys(function ($attr) {
                    return [$attr->attribute->name => $attr->value];
                })->toArray(),
                'selectedVariant' => $item->variant_id,
            ];
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
        return view('livewire.purchase.edit');
    }

    public function searchProduct($searchTerm)
    {
        $product = Product::with(['supplier', 'variants.attributeValues.attribute'])
            ->whereHas('supplier', fn($q) => $q->where('id', $this->selectedSupplier))
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

    public function saveCart()
    {
        DB::beginTransaction();

        try {
            $purchaseDate = $this->purchaseDate ? Carbon::parse($this->purchaseDate) : now();

            $purchase = Purchase::find($this->purchaseId);

            if ($purchase) {
                // Step 1: Restore previous stock before updating
                foreach ($purchase->items as $item) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->stock -= $item->quantity; // Decrease stock
                        $variant->save();
                    }
                }
                // Step 2: Update purchase
                $purchase->user_id = auth()->id();
                $purchase->note = $this->purchaseNote;
                $purchase->status = $this->purchaseStatus; // pending, complete, cancel
                $purchase->total_price = $this->calculateTotal();
                $purchase->purchase_date = $purchaseDate;
                $purchase->save();

                // Step 3: Delete old items and insert updated cart items
                $purchase->items()->delete();

                $purchase->items()->createMany(
                    collect($this->cart)->map(function ($item) {
                        return [
                            'variant_id' => $item['selectedVariant'],
                            'quantity' => $item['quantity'],
                            'price' => (float) $item['price'],
                        ];
                    })->toArray()
                );

                // Step 4: Increase stock based on updated cart
                foreach ($this->cart as $item) {
                    $variant = ProductVariant::find($item['selectedVariant']);
                    if ($variant) {
                        $variant->stock += $item['quantity']; // Increase stock
                        $variant->save();
                    }
                }
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
