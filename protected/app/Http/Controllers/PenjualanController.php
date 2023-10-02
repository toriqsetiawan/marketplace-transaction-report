<?php

namespace App\Http\Controllers;

use App\Entities\ConfigFee;
use App\Entities\Employee;
use App\Entities\Hutang;
use App\Entities\Product;
use App\Entities\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            $data = Transaction::where('name', 'like', '%' . $request->search . '%')
                ->orWhere('marketplace', $request->search)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $data = Transaction::with(['configFee', 'product'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('penjualan.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $products = Product::all();
        $marketplaces = ConfigFee::all();

        return view('penjualan.create', compact('products', 'marketplaces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
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

        Transaction::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'channel' => $request->channel,
            'marketplace' => $request->channel == 'online' ? $request->marketplace : 0,
            'jumlah' => $request->jumlah,
            'ukuran' => $request->ukuran,
            'motif' => $request->motif,
            'harga_beli' => $product->harga_beli,
            'harga_jual' => $hargaJual,
            'biaya_tambahan' => $request->packing ? $product->harga_tambahan : 0,
            'biaya_lain_lain' => $request->insole ? 5000 : 0,
            'pajak' => $pajak,
            'total_paid' => $hargaJual - $pajak,
            'status' => $request->status,
            'keterangan' => $request->keterangan ?? ''
        ]);

        return redirect()->back()
            ->with("success", "Sukses menambah transaksi")
            ->withInput();
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
        $employee = Employee::find($id);

        if (!$employee) {
            abort('404');
        }

        $data = Hutang::with(['employee'])
            ->where('employee_id', $employee->id)
            ->orderBy('created_at')
            ->paginate(10);

        return view('hutang.detail')
            ->with('employee', $employee)
            ->with('data', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Hutang::find($id);

        $employee = Employee::find($data->employee_id);

        return view('hutang.update')
            ->with('employee', $employee)
            ->with('data', $data);
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
            'employee' => 'required|exists:employee,id',
            'nama' => 'required|max:255',
            'harga' => 'required|numeric',
            'angsuran' => 'required|int',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $hutang = Hutang::find($id);

        $hutang->nama = $request->nama;
        $hutang->harga = $request->harga;
        $hutang->angsuran = $request->angsuran;
        $hutang->save();

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
