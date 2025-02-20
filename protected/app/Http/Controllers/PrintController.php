<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLog;
use App\Models\Report;
use App\Models\ReportDetail;
use App\Models\ReportGlobal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PrintController extends Controller
{

    public function index(Request $request)
    {
        $data = Employee::with(['report', 'bon', 'bon.detail', 'reportPrint' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        if ($request->has('type')) {
            if ($request->type == 'bulanan') {
                $data = $data->whereGolongan('bulanan');
            } elseif ($request->type == 'mingguan') {
                $data = $data->whereGolongan('mingguan');
            }
        } else {
            if ($request->has('search')) {
                $data = $data->where('nama', 'like', '%' . $request->search . '%');
            }
        }

        $data = $data->orderBy('nama')->paginate(10);

        return view('print.index')->with('data', $data);
    }

    public function show($id, Request $request)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            abort('404');
        }

        $data = ReportGlobal::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('print.detail')
            ->with('data', $data)
            ->with('employee', $employee);
    }

    public function edit($id, Request $request)
    {
        $report = ReportGlobal::with(['employee'])->find($id);

        if (!$report) {
            abort('404');
        }

        $first_br = $second_br = false;

        if ($request->first_enter == 'yes') {
            $first_br = true;
        }

        if ($request->second_enter == 'yes') {
            $second_br = true;
        }

        $arrData = [
            'data' => $report,
            'setor' => collect(json_decode($report->json_setor))->sortBy('date_at'),
            'bon' => collect(json_decode($report->json_bon)),
            'trx_log' => collect(json_decode($report->json_trx_log)),
            'first_enter' => $first_br,
            'second_enter' => $second_br,
        ];

        $pdf = \PDF::loadView('print.invoice', $arrData)
            ->setPaper('a4', 'potrait');

        return $pdf->stream('rekap-' . Str::slug($report->employee->nama) . '-' . date('d-m-Y') . '.pdf');
        // download('rekap-' . date('d_m_Y.pdf'));
    }

    public function recovery(Request $request)
    {
        $check = Report::where('employee_id', $request->id)->get()->first();
        if ($check instanceof Report) {
            return redirect()->to(url('print'))
                ->with("error", "Hapus setoran atau bon terlebih dahulu jika ingin mengedit transaksi sebelumnya.");
        }

        $report = ReportGlobal::where('employee_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->get()->first();

        if (!$report instanceof ReportGlobal) {
            return redirect()->to(url('print'))
                ->with("error", "Tidak ada transaksi sebelumnya.");
        }

        $dataSetor = collect(json_decode($report->json_setor))->sortBy('date_at');
        $dataBon = collect(json_decode($report->json_bon));
        $trx_log = collect(json_decode($report->json_trx_log));

        $report->delete();

        foreach ($dataSetor as $key) {
            $row = collect($key)->toArray();
            Report::create($row);
        }

        if (count($dataBon)) {
            $bon = [];

            foreach ($dataBon as $key) {
                $temp = collect($key)->toArray();
                Arr::forget($temp, 'varian');
                $bon[] = $temp;
            }

            Report::create([
                'id' => $bon[0]['report_id'],
                'employee_id' => $request->id,
                'type' => 'bon',
                'kodi' => 0,
                'total' => 0,
                'count' => 0,
                'date_at' => date('Y-m-d H:i:s'),
            ]);

            $bon = array_map(function ($record) {
                $record['created_at'] = Carbon::parse($record['created_at'])->format('Y-m-d H:i:s');
                $record['updated_at'] = Carbon::parse($record['updated_at'])->format('Y-m-d H:i:s');
                $record['date_at'] = Carbon::parse($record['date_at'])->format('Y-m-d');
                return $record;
            }, $bon);

            ReportDetail::insert($bon);
        }

        $log = EmployeeLog::where('employee_id', $request->id)->get()->first();
        $log->type = $trx_log['type'];
        $log->amount = $trx_log['amount'];
        $log->correction = $trx_log['correction'];
        $log->created_at = $trx_log['created_at'];
        $log->updated_at = $trx_log['updated_at'];
        $log->save();

        return redirect()->back()->with("success", "Sukses recovery data. Silahkan menuju menu Manajemen Transaksi untuk edit data.")->withInput();
    }

    public function weeklyReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        if ($request->tanggal_awal > $request->tanggal_akhir) {
            return redirect()->back()
                ->withError(['error' => 'Tanggal awal tidak boleh melebihi tanggal akhir.'])
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = Employee::with(['report', 'bon', 'log'])->orderBy('nama')->get();

        $arrData = [
            'data' => $data,
            'startdate' => $request->tanggal_awal,
            'enddate' => $request->tanggal_akhir,
        ];

        $pdf = \PDF::loadView('print.weekly', $arrData)
            ->setPaper('a4', 'potrait');

        return $pdf->stream('rekap-mingguan-' . date('d-m-Y') . '.pdf');
        // download('rekap-' . date('d_m_Y.pdf'));

    }

}
