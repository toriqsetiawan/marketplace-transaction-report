<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Product;
use Livewire\Component;

class Create extends Component
{
    public $searchTerm = '';
    public $products = [];
    public $cart = [];

    public function mount() {}

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
        // Filter products based on search term
        $products = Product::with(['variants', 'supplier'])->get();
        $filteredProducts = $products->filter(function ($product) {
            return stripos($product->nama, $this->searchTerm) !== false;
            // || (isset($product['barcode']) && stripos($product['barcode'], $this->searchTerm) !== false);
        })->all();

        return view('livewire.transaction.create', [
            'filteredProducts' => $filteredProducts,
            'cartTotal' => $this->calculateTotal(),
        ]);
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
                'harga_jual' => $product->harga_jual,
                'supplier' => [
                    'id' => $product->supplier['id'],
                    'name' => $product->supplier['name'],
                ],
                'variants' => array_map(function ($variant) {
                    return [
                        'id' => $variant['id'],
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                        'sku' => $variant['sku'],

                    ];
                }, $product->variants->toArray()), // Convert variants collection to array first
            ];
        })->toArray(); // Convert collection to an array
    }
}
