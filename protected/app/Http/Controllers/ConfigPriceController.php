<?php

namespace App\Http\Controllers;

use App\Entities\ConfigFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            $data = ConfigFee::where('marketplace', 'like', '%' . $request->search . '%')
                ->orderBy('marketplace')
                ->paginate(10);
        } else {
            $data = ConfigFee::orderBy('marketplace')->paginate(10);
        }

        return view('price.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('price.create');
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
            'marketplace' => 'required|in:shopee,tiktok,tokopedia,lazada',
            'persentase' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        ConfigFee::create($request->all());

        return redirect()->back()->with("success", "Sukses menambah konfigurasi biaya admin")->withInput();
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
        $config = ConfigFee::find($id);

        return view('price.update')->with('data', $config);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
