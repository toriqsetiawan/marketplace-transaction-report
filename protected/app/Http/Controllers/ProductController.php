<?php

namespace App\Http\Controllers;

use App\Entities\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            $data = Product::where('nama', 'like', '%' . $request->search . '%')
                ->orderBy('nama')
                ->paginate(10);
        } else {
            $data = Product::orderBy('nama')->paginate(10);
        }

        return view('product.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ukuran = [
            '26-30',
            '28-32',
            '29-33',
            '31-35',
            '33-37',
            '36-40',
            '39-43'
        ];
        return view('product.create')->with('ukuran', $ukuran);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'ukuran' => 'required',
            'harga_beli' => 'required',
            'harga_tambahan' => 'required',
            'harga_online' => 'required',
            'harga_offline' => 'required',
            'harga_mitra' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Product::create($request->all());

        return redirect()->back()->with("success", "Sukses menambah produk")->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $ukuran = [
            '26-30',
            '28-32',
            '29-33',
            '31-35',
            '33-37',
            '36-40',
            '39-43'
        ];

        return view('product.update')->with('data', $product)->with('ukuran', $ukuran);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'ukuran' => 'required',
            'harga_beli' => 'required',
            'harga_tambahan' => 'required',
            'harga_online' => 'required',
            'harga_offline' => 'required',
            'harga_mitra' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = Product::find($id);

        $product->nama = $request->nama;
        $product->ukuran = $request->ukuran;
        $product->harga_beli = $request->harga_beli;
        $product->harga_tambahan = $request->harga_tambahan;
        $product->harga_online = $request->harga_online;
        $product->harga_offline = $request->harga_offline;
        $product->harga_mitra = $request->harga_mitra;
        $product->save();

        return redirect()->back()->with("success", "Sukses merubah data")->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }
}
