<?php

namespace App\Http\Controllers;

use App\Entities\Employee;
use App\Entities\EmployeeLog;
use App\Entities\Report;
use App\Entities\ReportDetail;
use App\Entities\ReportGlobal;
use App\Entities\Varian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
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

        return view('transaction.index')->with('data', $data);
    }

    public function setor(Request $request)
    {
        $employee = Employee::find($request->id);

        if (!$employee) {
            abort('404');
        }

        //SELECT * FROM your_table WHERE YEARWEEK(`date`, 1) = YEARWEEK(CURDATE(), 1)

        $data = Report::where('employee_id', $employee->id)
            ->setor()
            ->orderBy('date_at', 'desc')
            ->get();

        $counter = $data->count() + 1;

        return view('transaction.create-setor')->with('employee', $employee)
            ->with('counter', $counter)
            ->with('data', collect($data)->sortBy('count'));
    }

    public function bon(Request $request)
    {
        $employee = Employee::find($request->id);

        if (!$employee instanceof Employee) {
            abort('404');
        }

        //SELECT * FROM your_table WHERE YEARWEEK(`date`, 1) = YEARWEEK(CURDATE(), 1)

        $data = Report::with(['detail.varian'])->where('employee_id', $employee->id)
            ->bon()
            ->orderBy('date_at', 'desc')
            ->get()->first();

        if ($data instanceof Report) {
            $data = $data->detail()->get();
        } else {
            $data = [];
        }

        $barang = Varian::orderBy('nama')->get();

        return view('transaction.create-bon')
            ->with('employee', $employee)
            ->with('data', $data)
            ->with('barang', $barang);
    }

    public function show(Request $request)
    {
        $employee = Employee::find($request->id);

        if (!$employee) {
            abort('404');
        }

        $setor = Report::where('employee_id', $employee->id)
            ->setor()
            ->orderBy('date_at')
            ->get();

        $employee_log = $employee->lastTransaction()->get()->first();

        $bon = Report::where('employee_id', $employee->id)
            ->bon()
            ->get()
            ->first();

        if ($bon instanceof Report) {
            $bon = $bon->detail()->orderBy('date_at')->get();
        } else {
            $bon = [];
        }

        return view('transaction.detail')
            ->with('setor', $setor)
            ->with('bon', $bon)
            ->with('employee', $employee)
            ->with('employee_log', $employee_log);
    }

    public function rekap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,id',
            'total_global' => 'required|numeric',
            'tunai' => 'required',
            // 'giro' => 'nullable',
        ]);

        $tunai = preg_replace("/[^\p{L}\p{N}\s]/u", "", $request->tunai);
        $giro = preg_replace("/[^\p{L}\p{N}\s]/u", "", $request->giro);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::find($request->employee_id);

        if (!$employee) {
            abort('404');
        }

        $data_setor = Report::where('employee_id', $employee->id)
            ->setor()
            ->orderBy('date_at', 'desc')
            ->get()
            ->toJson();

        $data_bon = $delete_bon = Report::where('employee_id', $employee->id)
            ->bon()
            ->orderBy('date_at', 'desc')
            ->get()->first();

        $data_last_trx = $employee->log()->get()->first();

        if ($data_bon instanceof Report) {
            $data_bon = $data_bon->detail()->with(['varian', 'varian.taxonomi'])->get()->toJson();
            $delete_bon->detail()->forceDelete();
        } else {
            $data_bon = json_encode([]);
        }

        Report::where('employee_id', $employee->id)->forceDelete();

        ReportGlobal::create([
            'employee_id' => $employee->id,
            'tunai' => $tunai,
            'giro' => $giro,
            'json_setor' => $data_setor,
            'json_bon' => $data_bon,
            'json_trx_log' => $data_last_trx,
        ]);

        $log_type = 'bon';

        $new_total_global = $request->total_global - ($tunai + $giro);

        if ($new_total_global >= 0) {
            $log_type = 'setor';
        }

        $log = EmployeeLog::where('employee_id', $employee->id)->get()->first();

        if ($log instanceof EmployeeLog) {
            $log->type = $log_type;
            $log->amount = abs($new_total_global);
            $log->correction = abs($new_total_global);
            $log->save();
        } else {
            EmployeeLog::create([
                'employee_id' => $employee->id,
                'type' => $log_type,
                'amount' => abs($new_total_global),
                'correction' => abs($new_total_global),
            ]);
        }

        return redirect()->back()->with("success", "Sukses merekap data")->withInput();
    }

    public function createSetor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,id',
            'type' => 'required|in:setor,bon',
            'count' => 'required|min:1|max:31',
            'total' => 'required',
            'kodi' => 'required|numeric',
            'date_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Report::create($request->all());

        return redirect()->back()->with("success", "Sukses menambah data")->withInput();
    }

    public function createBon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,id',
            'variant_id' => 'required|exists:varian,id',
            'quantity' => 'required',
            'date_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $check = Report::where('employee_id', $request->employee_id)
            ->bon()
            ->orderBy('date_at', 'desc')
            ->get()->first();

        if (!$check instanceof Report) {
            $check = Report::create([
                'employee_id' => $request->employee_id,
                'type' => 'bon',
                'kodi' => 0,
                'total' => 0,
                'count' => 0,
                'date_at' => $request->date_at,
            ]);
        }

        $varian = Varian::find($request->variant_id);

        ReportDetail::create([
            'varian_id' => $request->variant_id,
            'report_id' => $check->id,
            'quantity' => $request->quantity,
            'price_history' => $varian->harga_satuan,
            'sub_total' => $varian->harga_satuan * $request->quantity,
            'date_at' => $request->date_at,
        ]);

        return redirect()->back()->with("success", "Sukses menambah data")->withInput();
    }

    public function editSetor(Request $request)
    {
        $find = Report::find($request->id);

        if (!$find instanceof Report) {
            abort('404');
        }

        $employee = Employee::find($find->employee_id);

        if (!$employee instanceof Employee) {
            abort('404');
        }

        $data = Report::where('employee_id', $employee->id)
            ->setor()
            ->orderBy('date_at', 'desc')
            ->get();

        $counter = $data->count() + 1;

        return view('transaction.update-setor')->with('employee', $employee)
            ->with('counter', $counter)
            ->with('data', collect($data)->sortBy('count'))
            ->with('update', $find);
    }

    public function editBon(Request $request)
    {
        $detail = ReportDetail::find($request->id);

        if (!$detail instanceof ReportDetail) {
            abort('404');
        }

        $employee = Employee::find($detail->report->employee_id);

        if (!$employee instanceof Employee) {
            abort('404');
        }

        //SELECT * FROM your_table WHERE YEARWEEK(`date`, 1) = YEARWEEK(CURDATE(), 1)

        $data = Report::where('employee_id', $employee->id)
            ->bon()
            ->orderBy('date_at', 'desc')
            ->get()->first();

        if ($data instanceof Report) {
            $data = $data->detail()->get();
        }

        $barang = Varian::orderBy('nama')->get();

        return view('transaction.update-bon')
            ->with('employee', $employee)
            ->with('data', $data)
            ->with('update', $detail)
            ->with('barang', $barang);
    }

    public function updateSetor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,id',
            'type' => 'required|in:setor,bon',
            'count' => 'required|min:1|max:31',
            'total' => 'required|numeric',
            'kodi' => 'required|numeric',
            'date_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $update = Report::find($request->id);

        if (!$update instanceof Report) {
            abort('404');
        }

        $update->total = $request->total;
        $update->count = $request->count;
        $update->kodi = $request->kodi;
        $update->date_at = $request->date_at;
        $update->save();

        return redirect()->to(url('transaction/setor?id=' . $request->employee_id))
            ->with("success", "Sukses merubah data")
            ->withInput();
    }

    public function updateBon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,id',
            'variant_id' => 'required|exists:varian,id',
            'quantity' => 'required|numeric',
            'date_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $update = ReportDetail::find($request->id);

        if (!$update instanceof ReportDetail) {
            abort('404');
        }

        $varian = Varian::find($request->variant_id);

        $update->varian_id = $request->variant_id;
        $update->quantity = $request->quantity;
        $update->price_history = $varian->harga_satuan;
        $update->sub_total = $varian->harga_satuan * $request->quantity;
        $update->date_at = $request->date_at;
        $update->save();

        return redirect()->to(url('transaction/bon?id=' . $request->employee_id))
            ->with("success", "Sukses merubah data")
            ->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('setor')) {
            $data = Report::find($request->id);
            if (!$data instanceof Report) {
                return redirect()->back()->with("error", "Id not found")->withInput();
            }
        } else {
            $data = ReportDetail::find($request->id);
            if (!$data instanceof ReportDetail) {
                return redirect()->back()->with("error", "Id not found")->withInput();
            }
        }

        $data->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }
}
