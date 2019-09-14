<?php

namespace App\Http\Controllers;

use App\Entities\Taxonomi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaxonomiController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data = Taxonomi::satuan()->orderBy('nama')->paginate(10);

        return view('satuan.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('satuan.create');
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Taxonomi::create($request->all());

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
        $taxonomi = Taxonomi::find($id);

        return view('satuan.update')->with('data', $taxonomi);
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $taxonomi = Taxonomi::find($id);

        $taxonomi->nama = $request->nama;
        $taxonomi->save();

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
        $taxonomi = Taxonomi::find($id);
        $taxonomi->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }

}
