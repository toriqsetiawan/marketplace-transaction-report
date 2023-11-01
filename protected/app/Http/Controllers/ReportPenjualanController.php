<?php

namespace App\Http\Controllers;

use App\Entities\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $date = '2023-10-01 00:00:00';
        $start = Carbon::parse($date)->startOfMonth();
        $end = Carbon::parse($date)->endOfMonth();
        $transactions = Transaction::with(['configFee', 'mitra'])
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->where('status', 2)
            ->get()
            ->groupBy('channel');

        $data = [];

        foreach ($transactions as $marketplace) {
            foreach ($marketplace as $trx) {
                $data[$trx->channel][$trx->marketplace][] = $trx->total_paid - $trx->biaya_tambahan - $trx->biaya_lain_lain - $trx->harga_beli;
            }
        }

        // dd($data);

        return view('report-penjualan.index');
    }

    public function show(Request $request, $id)
    {
        $date = '2023-10-01 00:00:00';
        $start = Carbon::parse($date)->startOfMonth();
        $end = Carbon::parse($date)->endOfMonth();
        $transactions = Transaction::with(['configFee', 'mitra']);

        // todo filter
        $transactions->where('created_at', '>=', $start);
        $transactions->where('created_at', '<=', $end);

        switch ($request->mode) {
            case 'shopee':
                $transactions->where('marketplace', 1);
                break;
            case 'tiktok':
                $transactions->where('marketplace', 2);
                break;
            case 'tokopedia':
                $transactions->where('marketplace', 3);
                break;
            case 'lazada':
                $transactions->where('marketplace', 4);
                break;
            case 'mitra':
                $transactions->where('channel', 'mitra');
                break;
            case 'offline':
                $transactions->where('channel', 'offline');
                break;
            default:
                break;
        }

        $data = $transactions->paginate(25);

        return view('report-penjualan.show', compact('data'));
    }
}
