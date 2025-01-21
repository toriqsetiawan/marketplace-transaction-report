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
    public $isActiveVariant = false;

    public $tableRows = [];

    public function render()
    {
        $suppliers = Supplier::all();

        return view('livewire.product.creation', compact('suppliers'));
    }

    public function saveProduct()
    {
        dd($this->supplier, $this->productName, $this->hargaBeli, $this->hargaJual, $this->tableRows);
    }
}
