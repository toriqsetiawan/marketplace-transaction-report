<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay()->format('Y-m-d H:i:s')
            : now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay()->format('Y-m-d H:i:s')
            : now()->endOfMonth();

        $transactions = Transaction::with([
            'items' => function ($q) {
                $q->with(['variant.product'])
                    ->where('price', '>', 0);
                    // ->whereDoesntHave('variant.product', function ($q) {
                    //     $q->where('sku', 'like', 'packing-%')
                    //         ->orWhere('sku', 'like', 'insole-%');
                    // });
            },
            'user'
        ])
            ->whereBetween('created_at', [$start, $end])
            ->when($request->status, fn($q) => $q->where('status', 2))
            ->get();

        // Process transactions and calculate totals
        $transactionDetails = $transactions->map(function ($transaction) {
            // Filter items
            $filteredItems = $transaction->items->filter(function ($item) {
                return !str_starts_with($item->variant->sku ?? '', 'packing-') &&
                       !str_starts_with($item->variant->sku ?? '', 'insole-');
            });

            $totalBuyPrice = $filteredItems->sum(function ($item) {
                return ($item->variant->product->harga_beli ?? 0) * $item->quantity;
            });

            $totalQuantity = $filteredItems->sum('quantity');

            return [
                'date' => $transaction->created_at->format('Y-m-d'),
                'total_price' => $transaction->total_price,
                'total_buy_price' => $totalBuyPrice,
                'total_quantity' => $totalQuantity
            ];
        });

        // Group by date and sum totals for each date
        $groupedTransactions = $transactionDetails->groupBy('date')->map(function ($group) {
            return [
                'date' => $group->first()['date'],
                'total_price' => $group->sum('total_price'),
                'total_buy_price' => $group->sum('total_buy_price'),
                'total_quantity' => $group->sum('total_quantity')
            ];
        });

        // Optionally convert to array or collection
        $groupedTransactions = $groupedTransactions->values();

        $data = $groupedTransactions->toArray();

        return view('report-penjualan.index', compact('data'));
    }

    public function show(Request $request, $id)
    {
        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay()->format('Y-m-d H:i:s')
            : now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay()->format('Y-m-d H:i:s')
            : now()->endOfMonth();

        $transactions = Transaction::with([
            'items' => function ($q) {
                $q->with(['variant.product'])
                    ->where('price', '>', 0)
                    ->whereDoesntHave('variant.product', function ($q) {
                        $q->where('sku', 'like', 'packing-%')
                            ->orWhere('sku', 'like', 'insole-%');
                    });
            },
            'user'
        ])
            ->whereBetween('created_at', [$start, $end])
            ->when($request->status, fn($q) => $q->where('status', 2))
            ->get();

        return view('report-penjualan.show', compact('transactions'));
    }
}
