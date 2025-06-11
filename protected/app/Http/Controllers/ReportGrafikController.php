<?php

namespace App\Http\Controllers;

use App\Models\ReturnTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportGrafikController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['customer']);
        })->get();

        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfDay()->format('Y-m-d H:i:s')
            : now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->end_date)->endOfDay()->format('Y-m-d H:i:s')
            : now()->endOfMonth();

        $transactions = Transaction::with([
            'items' => function ($q) {
                $q->with([
                    'variant' => function ($q) {
                        $q->withTrashed()
                        ->with(['product.supplier']);
                    }
                ]);
            },
            'user'
        ])
            ->whereBetween('created_at', [$start, $end])
            ->whereNot('status', 'return')
            ->when($request->user, fn($q) => $q->whereIn('user_id', explode(',', $request->user)))
            ->get();

        // Process transactions and group by product
        $productSales = [];
        $dates = [];

        foreach ($transactions as $transaction) {
            $date = $transaction->created_at->format('Y-m-d');
            $dates[$date] = $date;

            foreach ($transaction->items as $item) {
                if (!$item->variant?->product) continue;

                $productName = $item->variant->product->nama;
                // Skip products containing SOLE or PACKING
                if (stripos($productName, 'SOLE') !== false || stripos($productName, 'PACKING') !== false) {
                    continue;
                }

                $productId = $item->variant->product->id;

                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = [
                        'name' => $productName,
                        'dates' => [],
                        'total_quantity' => 0,
                        'total_sales' => 0
                    ];
                }

                if (!isset($productSales[$productId]['dates'][$date])) {
                    $productSales[$productId]['dates'][$date] = [
                        'quantity' => 0,
                        'sales' => 0
                    ];
                }

                $quantity = $item->quantity;
                $sales = $item->price * $quantity;

                $productSales[$productId]['dates'][$date]['quantity'] += $quantity;
                $productSales[$productId]['dates'][$date]['sales'] += $sales;
                $productSales[$productId]['total_quantity'] += $quantity;
                $productSales[$productId]['total_sales'] += $sales;
            }
        }

        // Sort dates
        $dates = array_keys($dates);
        sort($dates);

        // Prepare formatted dates for graph labels (d-m-Y)
        $formattedDates = array_map(function($date) {
            return Carbon::parse($date)->format('d-m-Y');
        }, $dates);

        // Prepare data for the graph
        $graphData = [];
        foreach ($productSales as $productId => $data) {
            $quantities = [];
            $sales = [];
            $totalQuantity = 0;
            $dailyQuantities = [];

            foreach ($dates as $date) {
                $quantity = $data['dates'][$date]['quantity'] ?? 0;
                $quantities[] = $quantity;
                $sales[] = $data['dates'][$date]['sales'] ?? 0;
                $totalQuantity += $quantity;
                $dailyQuantities[$date] = $quantity;
            }

            $graphData[] = [
                'id' => $productId,
                'name' => $data['name'],
                'dates' => $formattedDates, // for graph labels
                'quantities' => $quantities,
                'sales' => $sales,
                'total_quantity' => $totalQuantity,
                'total_sales' => $data['total_sales'],
                'daily_quantities' => $dailyQuantities // Y-m-d keys
            ];
        }

        // Sort products by total quantity
        usort($graphData, function($a, $b) {
            return $b['total_quantity'] <=> $a['total_quantity'];
        });

        // Create a copy for the summary table with all products
        $summaryData = $graphData;

        // Limit to top 10 products for graphs
        $graphData = array_slice($graphData, 0, 10);

        // Debug data
        \Log::info('Graph Data:', [
            'dates' => $dates,
            'graphData' => $graphData
        ]);

        return view('report-grafik.index', compact(
            'users',
            'graphData',
            'summaryData',
            'dates'
        ));
    }

    public function show(Request $request, $id)
    {

    }
}
