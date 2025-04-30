<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $data = Product::with(['supplier', 'variants.attributeValues' => function ($q) {
            $q->with('attribute')
                ->orderBy('value', 'asc');
        }]);

        if ($request->has('search')) {
            $data->where('nama', 'like', '%' . $request->search . '%');
        }

        $data = $data->orderBy('id', 'desc')
            ->paginate(20);

        $supplier = Supplier::all();

        return view('stock.index')->with('data', $data)
            ->with('supplier', $supplier);
    }
}
