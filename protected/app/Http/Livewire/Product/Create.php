<?php

namespace App\Http\Livewire\Product;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{
    public $supplier;
    public $productName;
    public $hargaBeli;
    public $hargaJual;
    public $isActiveVariant = false;

    public $tableRows = [];
    public $variations = [];

    public function mount()
    {
        $this->variations = [
            ['name' => 'Warna', 'options' => ['']],
            ['name' => 'Ukuran', 'options' => ['']],
        ];
    }

    public function render()
    {
        $suppliers = Supplier::all();

        return view('livewire.product.create', compact('suppliers'));
    }

    public function saveProduct()
    {
        DB::beginTransaction();

        try {
            // 1. Create a Product
            $product = Product::create([
                'supplier_id' => $this->supplier,
                'nama' => $this->productName,
                'harga_beli' => $this->hargaBeli,
                'harga_jual' => $this->hargaJual,
            ]);

            // 2. Create Attributes
            foreach ($this->variations as $variation) {
                $attribute = Attribute::create([
                    'name' => strtoupper($variation['name'])
                ]);

                // 3. Create Attribute Values
                foreach ($variation['options'] as $option) {
                    $AttributeValue = AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => strtoupper($option)
                    ]);
                }
            }

            // 4. Create Variants for the Product
            foreach ($this->tableRows as $row) {
                // Create the ProductVariant
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'price' => $row['harga'],
                    'stock' => $row['stok'],
                    'sku' => $row['kode'], // Assuming 'kode' is the SKU
                ]);

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

            DB::commit();
            session()->flash('success', 'Sukses menambah/merubah produk');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Gagal menambah/merubah produk: ' . $th->getMessage());
        }
    }
}
