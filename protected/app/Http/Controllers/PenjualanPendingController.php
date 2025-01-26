<?php

namespace App\Http\Controllers;

use App\Models\ConfigFee;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenjualanPendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay()->format('Y-m-d H:i:s')
            : null;
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay()->format('Y-m-d H:i:s')
            : null;

        $data = Transaction::with(['configFee', 'product', 'user'])
            ->where('status', 1);

        if ($start) {
            $data->where('created_at', '>=', $start);
        }

        if ($end) {
            $data->where('created_at', '<=', $end);
        }

        if ($request->has('search')) {
            if ($request->mitra) {
                $data->where('name', $request->mitra);
            } else {
                $data->where('name', 'like', '%' . $request->search . '%');
            }
            $data->orWhere('marketplace', $request->search);
        }

        $data = $data->orderBy('created_at', 'desc')
            ->paginate(20);

        $listUser = User::all();

        return view('penjualan-pending.index')
            ->with('data', $data)
            ->with('listUser', $listUser);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

    }

    private function calculateTax($request, $hargaJual)
    {
        if ($request->channel == 'online') {
            $fee = ConfigFee::findOrFail($request->marketplace);
            return $fee->persentase * $hargaJual;
        }

        return 0;
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
        $data = Transaction::findOrFail($id);
        $products = Product::all();
        $marketplaces = ConfigFee::all();
        $user = User::all();

        return view('penjualan-pending.update', compact('data', 'products', 'marketplaces', 'user'));
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
            'channel' => 'required|in:online,offline,mitra',
            'marketplace' => 'required_if:channel,online',
            'name' => 'required',
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
            'ukuran' => 'required|integer|min:26|max:43',
            'motif' => 'required',
            'date_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = Product::findOrFail($request->product_id);

        $variableHargaJual = 'harga_' . $request->channel;
        $hargaJual = $product->$variableHargaJual;
        $pajak = $this->calculateTax($request, $hargaJual);

        $transaction = Transaction::findOrFail($id);
        $transaction->product_id = $request->product_id;
        $transaction->name = $request->name;
        $transaction->channel = $request->channel;
        $transaction->marketplace = $request->channel == 'online' ? $request->marketplace : 0;
        $transaction->jumlah = $request->jumlah;
        $transaction->ukuran = $request->ukuran;
        $transaction->motif = $request->motif;
        $transaction->harga_beli = $product->harga_beli;
        $transaction->harga_jual = $hargaJual;
        $transaction->biaya_tambahan = $request->packing ? $product->harga_tambahan : 0;
        $transaction->biaya_lain_lain = $request->insole ? 5000 : 0;
        $transaction->pajak = $pajak;
        $transaction->total_paid = $request->harga ?? ($hargaJual - $pajak);
        $transaction->status = $request->status;
        $transaction->keterangan = $request->keterangan ?? '';

        if ($request->date_at) {
            $transaction->timestamps = false;
            $transaction->created_at = Carbon::parse($request->date_at)->format('Y-m-d H:i:s');
            $transaction->updated_at = now();
        }

        $transaction->save();

        return redirect()->back()
            ->with("success", "Sukses merubah data")
            ->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

    }
}
