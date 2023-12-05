<?php

namespace App\Http\Controllers;

use App\Entities\Mitra;
use App\Entities\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = Supplier::orderBy('nama')->paginate(20);

        if ($request->has('search')) {
            $data = Supplier::where('nama', 'like', '%' . $request->search . '%')
                ->orderBy('nama')
                ->paginate(20);
        } else {
            $data = Supplier::orderBy('nama')->paginate(20);
        }

        return view('supplier.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Supplier::create($request->all());

        return redirect()->back()->with("success", "Sukses menambah data")->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $mitra = Mitra::findOrFail($id);

        return view('supplier.update')
            ->with('data', $mitra);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $supplier = Supplier::findOrFail($id);
        $supplier->nama = $request->nama;
        $supplier->keterangan = $request->keterangan;
        $supplier->save();

        return redirect()->back()->with("success", "Sukses merubah data")->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }
}
