<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay()->format('Y-m-d H:i:s')
            : now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay()->format('Y-m-d H:i:s')
            : now()->endOfMonth();

        $data = Purchase::with(['items.variant.product.variants.attributeValues.attribute', 'user'])
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end);

        $data = $data->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('purchase.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        return view('purchase.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return view('purchase.update', ['purchaseId' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }
}
