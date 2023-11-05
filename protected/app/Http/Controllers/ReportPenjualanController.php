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
            ->get();

        $data = [];

        foreach ($transactions->filter(fn ($q) => $q->channel == 'online')->groupBy('channel') as $marketplace) {
            foreach ($marketplace as $trx) {
                $data[$trx->channel][$trx->marketplace][] = ($trx->total_paid - $trx->biaya_tambahan - $trx->biaya_lain_lain - $trx->harga_beli) * $trx->jumlah;
            }
        }

        foreach ($transactions->filter(fn ($q) => $q->channel == 'mitra')->groupBy('name') as $marketplace) {
            foreach ($marketplace as $trx) {
                $data[$trx->channel][$trx->mitra->nama][] = ($trx->total_paid - $trx->biaya_tambahan - $trx->biaya_lain_lain - $trx->harga_beli) * $trx->jumlah;
            }
        }

        foreach ($transactions->filter(fn ($q) => $q->harga_beli > 15000) as $transaction) {
            $data['gudang'][] = $transaction->harga_beli * $trx->jumlah;
        }

        foreach ($transactions->filter(fn ($q) => in_array($q->product_id, [40, 41, 42, 43, 44])) as $transaction) {
            $data['trading'][] = $transaction->harga_beli * $trx->jumlah;
        }

        return view('report-penjualan.index', compact('data'));
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
            case 'gudang':
                $transactions->where('harga_beli', '>', 15000)
                    ->whereNotIn('product_id', [40, 41, 42, 43, 44]);
                break;
            case 'trading':
                $transactions->whereIn('product_id', [40, 41, 42, 43, 44]);
                break;
            default:
                break;
        }

        $data = $transactions->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('report-penjualan.show', compact('data'));
    }
}
