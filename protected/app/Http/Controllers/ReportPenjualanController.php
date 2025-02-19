<?php

namespace App\Http\Controllers;

use App\Models\ReturnTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportPenjualanController extends Controller
{
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

        $transactions = Transaction::with([
            'items' => function ($q) {
                $q->with(['variant.product.supplier'])
                    ->where('price', '>', 0);
            },
            'user'
        ])
            ->whereBetween('created_at', [$start, $end])
            ->when($request->user, fn($q) => $q->where('user_id', $request->user))
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
                'total_quantity' => $totalQuantity,
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

        // Report by supplier
        $supplierSales = [];

        foreach ($transactions as $transaction) {
            $filteredItems = $transaction->items->filter(function ($item) {
                return !str_starts_with($item->variant->sku ?? '', 'packing-') &&
                    !str_starts_with($item->variant->sku ?? '', 'insole-');
            });

            foreach ($filteredItems as $item) {
                $supplier = $item->variant->product->supplier;
                $supplierName = $supplier->name ?? 'Unknown Supplier';

                // Initialize supplier data if not already present
                if (!isset($supplierSales[$supplierName])) {
                    $supplierSales[$supplierName] = [
                        'totalQuantity' => 0,
                        'totalPrice' => 0,
                    ];
                }

                // Update the total quantity and total price for the supplier
                $supplierSales[$supplierName]['totalQuantity'] += $item->quantity;
                $supplierSales[$supplierName]['totalPrice'] += $item->variant->product->harga_beli * $item->quantity;
            }
        }

        $returnTransactions = ReturnTransaction::with([
            'items' => function ($q) {
                $q->with(['variant.product.supplier'])
                    ->where('price', '>', 0);
            },
            'user'
        ])
            ->whereBetween('created_at', [$start, $end])
            ->when($request->user, fn($q) => $q->where('user_id', $request->user))
            ->get();

        $supplierReturn = [];

        foreach ($returnTransactions as $transaction) {
            $filteredItems = $transaction->items->filter(function ($item) {
                return !str_starts_with($item->variant->sku ?? '', 'packing-') &&
                    !str_starts_with($item->variant->sku ?? '', 'insole-');
            });

            foreach ($filteredItems as $item) {
                $supplier = $item->variant->product->supplier;
                $supplierName = $supplier->name ?? 'Unknown Supplier';

                // Initialize supplier data if not already present
                if (!isset($supplierReturn[$supplierName])) {
                    $supplierReturn[$supplierName] = [
                        'totalQuantity' => 0,
                        'totalPrice' => 0,
                    ];
                }

                // Update the total quantity and total price for the supplier
                $supplierReturn[$supplierName]['totalQuantity'] += $item->quantity;
                $supplierReturn[$supplierName]['totalPrice'] += $item->variant->product->harga_beli * $item->quantity;
            }
        }

        return view('report-penjualan.index', compact(
            'users',
            'data',
            'supplierSales',
            'supplierReturn',
        ));
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
            ->when($request->user, fn($q) => $q->where('user_id', $request->user))
            ->get();

        return view('report-penjualan.show', compact('transactions'));
    }
}
