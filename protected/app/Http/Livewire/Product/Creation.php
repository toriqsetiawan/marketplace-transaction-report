<?php

namespace App\Http\Livewire\Product;

use App\Models\Supplier;
use Livewire\Component;

class Creation extends Component
{
    public $supplier;
    public $productName;
    public $hargaBeli;
    public $hargaJual;
    public $price;
    public $variants = [];
    public $isActiveVariant = false;

    public $variations = [];

    public function render()
    {
        $suppliers = Supplier::all();

        return view('livewire.product.creation', compact('suppliers'));
    }
}
