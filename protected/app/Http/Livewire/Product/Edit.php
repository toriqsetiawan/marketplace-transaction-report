<?php

namespace App\Http\Livewire\Product;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public $productId;

    public $supplier;
    public $productName;
    public $hargaBeli;
    public $hargaJual;
    public $isActiveVariant = false;

    public $tableRows = [];
    public $variations = [];

    public function mount(Product $product)
    {
        $this->productId = $product->id;

        // Retrieve the product
        $product = Product::with(['variants.attributeValues.attribute'])->findOrFail($product->id);

        $this->supplier = $product->supplier_id;
        $this->productName = $product->nama;
        $this->hargaBeli = $product->harga_beli;
        $this->hargaJual = $product->harga_jual;

        // Initialize variations
        $this->variations = $this->buildVariations($product);

        if (count($this->variations) > 0) {
            $this->isActiveVariant = true;
        }

        // Initialize tableRows
        $this->tableRows = $this->buildTableRows($product);
    }

    private function buildVariations($product)
    {
        // Extract attributes and their options from variants
        $variations = [];

        foreach ($product->variants as $variant) {
            foreach ($variant->attributeValues as $attributeValue) {
                $attributeName = $attributeValue->attribute->name;
                $value = $attributeValue->value;

                if (!isset($variations[$attributeName])) {
                    $variations[$attributeName] = [];
                }

                if (!in_array($value, $variations[$attributeName])) {
                    $variations[$attributeName][] = $value;
                }
            }
        }

        // Transform into the desired structure
        return collect($variations)
            ->map(function ($options, $name) {
                return [
                    'name' => $name,
                    'options' => $options,
                ];
            })
            ->values()
            ->toArray();
    }

    private function buildTableRows($product)
    {
        // Build rows from variants
        return $product->variants->map(function ($variant) {
            $row = [
                'harga' => $variant->price,
                'stok' => $variant->stock,
                'kode' => $variant->sku,
            ];

            foreach ($variant->attributeValues as $attributeValue) {
                $attributeName = strtolower($attributeValue->attribute->name);
                $row[$attributeName] = $attributeValue->value;
            }

            return $row;
        })->toArray();
    }

    public function render()
    {
        $suppliers = Supplier::all();

        return view('livewire.product.edit', compact('suppliers'));
    }

    public function saveProduct()
    {
        DB::beginTransaction();

        try {
            // Update or Create the Product
            $product = Product::with(['variants.attributeValues'])->findOrFail($this->productId); // Assuming $this->productId is passed for updates
            $product->supplier_id = $this->supplier;
            $product->nama = $this->productName;
            $product->harga_beli = $this->hargaBeli;
            $product->harga_jual = $this->hargaJual;
            $product->save();

            // Delete existing attributeValues
            // foreach ($product->variants as $variant) {
            //     $variant->attributeValues()->delete();
            // }

            // Handle Attributes and Attribute Values
            $attributeMap = [];
            foreach ($this->variations as $variation) {
                // Find or create the attribute
                $attribute = Attribute::where('name', strtoupper($variation['name']))->first();
                if (!$attribute) {
                    $attribute = Attribute::create([
                        'name' => strtoupper($variation['name'])
                    ]);
                }

                // Sync attribute values
                foreach ($variation['options'] as $option) {
                    $attributeValue = AttributeValue::where([
                        'attribute_id' => $attribute->id,
                        'value' => strtoupper($option)
                    ])->first();

                    if (!$attributeValue) {
                        $attributeValue = AttributeValue::create([
                            'attribute_id' => $attribute->id,
                            'value' => strtoupper($option)
                        ]);
                    }

                    // Map attribute and value for quick reference
                    $attributeMap[strtoupper($variation['name'])][strtoupper($option)] = $attributeValue->id;
                }
            }

            // Get Existing Variants
            $existingVariantIds = $product->variants->pluck('id')->toArray();
            $submittedVariantIds = [];

            // Create or Update Variants for the Product
            foreach ($this->tableRows as $row) {
                // Create the ProductVariant
                $variant = ProductVariant::where([
                    'product_id' => $product->id,
                    'sku' => $row['kode'],
                ])->first();

                if ($variant) {
                    $variant->update([
                        'price' => $row['harga'],
                        'stock' => $row['stok'],
                    ]);
                } else {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'price' => $row['harga'],
                        'stock' => $row['stok'],
                        'sku' => $row['kode'] ?? '',
                    ]);

                    if (!$row['kode']) {
                        $fullSku = ProductVariant::where('product_id', $product->id)->first()?->sku;
                        $parentSku = Str::beforeLast($fullSku, '-'); // Removes the last -xxx part
                        $row['kode'] = $parentSku;
                    }

                    $variant->update(['sku' => $row['kode'] . '-' . $variant->id]);
                }

                // Store variant IDs that should exist
                $submittedVariantIds[] = $variant->id;

                // Attach AttributeValues to the ProductVariant
                $attributeValueIds = [];
                foreach ($this->variations as $variation) {
                    $attributeName = strtoupper($variation['name']);
                    $attributeValue = strtoupper($row[strtolower($attributeName)]);

                    // Find the corresponding AttributeValue
                    $valueId = AttributeValue::whereHas('attribute', function ($query) use ($attributeName) {
                        $query->where('name', $attributeName);
                    })->where('value', $attributeValue)->value('id');

                    if ($valueId) {
                        $attributeValueIds[] = $valueId;
                    }
                }

                // Attach all matching AttributeValues to the variant
                $variant->attributeValues()->attach($attributeValueIds);
            }

            // Delete Variants That Are No Longer Present
            $variantsToDelete = array_diff($existingVariantIds, $submittedVariantIds);
            ProductVariant::whereIn('id', $variantsToDelete)->delete();

            DB::commit();
            session()->flash('success', 'Produk berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Gagal memperbarui produk: ' . $th->getMessage());
        }
    }
}
