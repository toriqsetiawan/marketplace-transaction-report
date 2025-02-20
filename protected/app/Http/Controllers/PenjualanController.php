<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['reseller', 'customer']);
        })->get();

        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay()->format('Y-m-d H:i:s')
            : now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay()->format('Y-m-d H:i:s')
            : now()->endOfMonth();

        $data = Transaction::with(['items.variant.product.variants.attributeValues.attribute', 'user'])
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->when($request->user, fn($q) => $q->where('user_id', $request->user))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('penjualan.index', compact('data', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        return view('penjualan.create');
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
        return view('penjualan.update', ['transactionId' => $id]);
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
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }

    // public function processReturn($transactionId)
    // {
    //     $transaction = Transaction::findOrFail($this->transactionId);
    //     $totalRefund = 0;

    //     $productReturn = ProductReturn::create([
    //         'transaction_id' => $transaction->id,
    //         'return_date' => now(),
    //         'status' => 'pending',
    //     ]);

    //     foreach ($this->returns as $itemId => $returnData) {
    //         $transactionItem = TransactionItem::findOrFail($itemId);
    //         $returnQuantity = $returnData['quantity'];

    //         if ($returnQuantity > 0 && $returnQuantity <= $transactionItem->quantity) {
    //             $refundAmount = $returnQuantity * $transactionItem->price;
    //             $totalRefund += $refundAmount;

    //             ProductReturnItem::create([
    //                 'product_return_id' => $productReturn->id,
    //                 'transaction_item_id' => $transactionItem->id,
    //                 'quantity' => $returnQuantity,
    //                 'refund_amount' => $refundAmount,
    //             ]);

    //             // Adjust stock
    //             $transactionItem->productVariant->increment('stock', $returnQuantity);

    //             // Update transaction item
    //             $transactionItem->quantity -= $returnQuantity;
    //             $transactionItem->save();
    //         }
    //     }

    //     $productReturn->update([
    //         'total_refund' => $totalRefund,
    //         'status' => 'processed',
    //     ]);

    //     session()->flash('message', 'Return processed successfully.');
    // }
}
