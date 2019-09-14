<?php

namespace App\Http\Controllers;

use App\Entities\Taxonomi;
use App\Entities\Varian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VarianController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            $data = Varian::where('nama', 'like', '%' . $request->search . '%')
                ->where('type', 'item')
                ->orderBy('nama')
                ->paginate(10);
        } else {
            $data = Varian::where('type', 'item')->orderBy('nama')->paginate(10);
        }

        return view('varian.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $taxonomi = Taxonomi::orderBy('nama')->get();
        return view('varian.create')->with('satuan', $taxonomi);
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
            'harga_satuan' => 'required',
            'taxonomi_id' => 'required|exists:taxonomy,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Varian::create($request->all());

        return redirect()->back()->with("success", "Sukses menambah data")->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $varian = Varian::find($id);
        $taxonomi = Taxonomi::orderBy('nama')->get();

        return view('varian.update')->with('data', $varian)->with('satuan', $taxonomi);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga_satuan' => 'required',
            'taxonomi_id' => 'required|exists:taxonomy,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $varian = Varian::find($id);

        $varian->nama = $request->nama;
        $varian->taxonomi_id = $request->taxonomi_id;
        $varian->harga_satuan = $request->harga_satuan;
        $varian->save();

        return redirect()->back()->with("success", "Sukses merubah data")->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $varian = Varian::find($id);
        $varian->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }

}
