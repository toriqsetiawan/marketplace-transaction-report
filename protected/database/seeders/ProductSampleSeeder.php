<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create a Product
        $product = Product::create([
            'supplier_id' => Supplier::first()->id,
            'nama' => 'Tosant badminton test',
            'harga_beli' => 54500,
            'harga_jual' => 115000,
        ]);

        // 2. Create Attributes
        $colorAttribute = Attribute::create(['name' => 'Color']);
        $sizeAttribute = Attribute::create(['name' => 'Size']);

        // 3. Create Attribute Values
        $blackValue = AttributeValue::create(['attribute_id' => $colorAttribute->id, 'value' => 'Black']);
        $blueValue = AttributeValue::create(['attribute_id' => $colorAttribute->id, 'value' => 'Blue']);
        $size39Value = AttributeValue::create(['attribute_id' => $sizeAttribute->id, 'value' => '39']);
        $size40Value = AttributeValue::create(['attribute_id' => $sizeAttribute->id, 'value' => '40']);

        // 4. Create Variants for the Product
        $variant1 = ProductVariant::create([
            'product_id' => $product->id,
            'price' => 100000,
            'sku' => '12345',
            'stock' => 10
        ]);
        $variant1->attributeValues()->attach([
            $blackValue->id, // Black
            $size39Value->id, // Size 39
        ]);

        $variant2 = ProductVariant::create([
            'product_id' => $product->id,
            'price' => 120000,
            'sku' => '12345',
            'stock' => 10
        ]);
        $variant2->attributeValues()->attach([
            $blackValue->id, // Black
            $size40Value->id, // Size 40
        ]);

        $variant3 = ProductVariant::create([
            'product_id' => $product->id,
            'price' => 110000,
            'sku' => '12345',
            'stock' => 10
        ]);
        $variant3->attributeValues()->attach([
            $blueValue->id, // Blue
            $size39Value->id, // Size 39
        ]);

        // 5. Output for Debugging (Optional)
        $this->command->info("Database seeded successfully!");
    }
}
