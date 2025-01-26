<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Soni Jaya',
                'keterangan' => '',
            ],
            [
                'name' => 'Art Footwear',
                'keterangan' => '',
            ]
        ];

        foreach ($data as $supplier) {
            Supplier::create($supplier);
        }
    }
}
