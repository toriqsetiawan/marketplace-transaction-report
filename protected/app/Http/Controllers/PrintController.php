<?php

namespace App\Http\Controllers;

use App\Entities\Employee;
use App\Entities\ReportGlobal;
use Illuminate\Http\Request;

class PrintController extends Controller
{

    public function index(Request $request)
    {
        $data = Employee::orderBy('golongan')->paginate(10);

        if ($request->has('type')) {
            if ($request->type == 'bulanan') {
                $data = Employee::whereGolongan('bulanan')->orderBy('nama')->paginate(10);
            } elseif ($request->type == 'mingguan') {
                $data = Employee::whereGolongan('mingguan')->orderBy('nama')->paginate(10);
            }
        } else {
            if ($request->has('search')) {
                $data = Employee::with(['report', 'bon.detail'])
                    ->where('nama', 'like', '%' . $request->search . '%')
                    ->orderBy('nama')
                    ->paginate(10);
            } else {
                $data = Employee::with(['report', 'bon.detail'])
                    ->orderBy('nama')
                    ->paginate(10);
            }
        }

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

    public function edit($id)
    {
        $report = ReportGlobal::with(['employee'])->find($id);

        if (!$report) {
            abort('404');
        }

        $arrData = [
            'data' => $report,
            'setor' => collect(json_decode($report->json_setor))->sortBy('date_at'),
            'bon' => collect(json_decode($report->json_bon)),
            'trx_log' => collect(json_decode($report->json_trx_log)),
        ];

        $pdf = \PDF::loadView('print.invoice', $arrData)
            ->setPaper('a4', 'potrait');

        return $pdf->stream('rekap-' . str_slug($report->employee->nama) . '-' . date('d-m-Y.pdf')); //download('rekap-' . date('d_m_Y.pdf'));
    }
}
