<?php

namespace App\Http\Controllers;

use App\Entities\Supplier;
use App\Entities\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfMonth()->format('Y-m-d H:i:s')
            : now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->endOfMonth()->format('Y-m-d H:i:s')
            : now()->endOfMonth();

        $transactions = Transaction::with(['configFee', 'mitra', 'product.supplier'])
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->where('status', 2)
            ->get();

        $data = [];

        foreach ($transactions->filter(fn ($q) => $q->channel == 'online')->groupBy('channel') as $trxMarketplace) {
            foreach ($trxMarketplace as $online) {
                $data[$online->channel][$online->marketplace][] = ($online->total_paid - $online->biaya_tambahan - $online->biaya_lain_lain - $online->harga_beli) * $online->jumlah;
            }
        }

        foreach ($transactions->filter(fn ($q) => $q->channel == 'mitra')->groupBy('name') as $trxMitra) {
            foreach ($trxMitra as $mitra) {
                $data[$mitra->channel][$mitra->mitra->nama][] = ($mitra->total_paid - $mitra->biaya_tambahan - $mitra->biaya_lain_lain - $mitra->harga_beli) * $mitra->jumlah;
            }
        }

        foreach ($transactions->filter(fn ($q) => $q->channel == 'offline') as $offline) {
            $data['offline'][] = ($offline->total_paid - $offline->biaya_tambahan - $offline->biaya_lain_lain - $offline->harga_beli) * $offline->jumlah;
        }

        $supplier = Supplier::all();

        foreach ($supplier as $key) {
            $filteredData = $transactions->filter(function ($q) use ($key) {
                if ($q->product) {
                    if ($q->product->supplier) {
                        return $q->product->supplier->id == $key->id;
                    }
                }
                return null;
            });
            foreach ($filteredData as $trx) {
                $data[$key->nama][] = $trx->harga_beli * $trx->jumlah;
            }
        }

        return view('report-penjualan.index', compact('data', 'supplier'));
    }

    public function show(Request $request, $id)
    {
        $start = $request->start_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->startOfMonth()->format('Y-m-d H:i:s')
            : now()->startOfMonth();
        $end = $request->end_date
            ? Carbon::createFromFormat('d/m/Y', $request->start_date)->endOfMonth()->format('Y-m-d H:i:s')
            : now()->endOfMonth();

        $transactions = Transaction::with(['product.supplier', 'configFee', 'mitra']);

        // todo filter
        $transactions->where('created_at', '>=', $start);
        $transactions->where('created_at', '<=', $end);

        $ownerName = null;

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
                $ownerName = Supplier::find($request->mode);
                $transactions->whereHas('product', fn ($q) => $q->where('supplier_id', $request->mode));
                break;
        }

        $data = $transactions->orderBy('created_at', 'desc')
            ->paginate(25);

        $ownerName = $ownerName ? 'Produk dari ' . $ownerName->nama : null;

        return view('report-penjualan.show', compact('data', 'ownerName'));
    }
}
