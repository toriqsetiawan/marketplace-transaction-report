<?php

namespace App\Http\Controllers;

use App\Entities\Employee;
use App\Entities\EmployeeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = Employee::withoutGlobalScopes()->orderBy('nama')->paginate(10);

        if ($request->has('type')) {
            if ($request->type == 'bulanan') {
                $data = Employee::withoutGlobalScopes()->whereGolongan('bulanan')
                    ->orderBy('nama')->paginate(10);
            } elseif ($request->type == 'mingguan') {
                $data = Employee::withoutGlobalScopes()->whereGolongan('mingguan')
                    ->orderBy('nama')->paginate(10);
            }
        } else {
            if ($request->has('search')) {
                $data = Employee::withoutGlobalScopes()->where('nama', 'like', '%' . $request->search . '%')
                    ->orderBy('nama')
                    ->paginate(10);
            } else {
                $data = Employee::withoutGlobalScopes()->orderBy('nama')->paginate(10);
            }
        }

        return view('pegawai.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('pegawai.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat' => 'required',
            'phone' => 'required|numeric',
            'golongan' => 'required|in:bulanan,mingguan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::create($request->all());
        EmployeeLog::create([
            'employee_id' => $employee->id,
            'type' => 'setor',
            'amount' => 0,
            'correction' => 0,
        ]);

        return redirect()->back()->with("success", "Sukses menambah data")->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $employee = Employee::withoutGlobalScopes()->find($id);
        $employee_log = EmployeeLog::where('employee_id', $employee->id)->get()->first();

        return view('pegawai.update')
            ->with('data', $employee)
            ->with('employee_log', $employee_log);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat' => 'required',
            'phone' => 'required|numeric',
            'golongan' => 'required|in:bulanan,mingguan',
            'total_trx' => 'required|min:0',
            'trx_type' => 'in:setor,bon',
            'status' => 'in:1,0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::withoutGlobalScopes()->find($id);

        $employee->nama = $request->nama;
        $employee->alamat = $request->alamat;
        $employee->phone = $request->phone;
        $employee->golongan = $request->golongan;
        $employee->is_active = $request->status ? 1 : 0;
        $employee->save();

        $log = EmployeeLog::where('employee_id', $employee->id)->get()->first();

        $log->amount = $request->total_trx;
        $log->correction = $request->total_trx;
        $log->type = $request->trx_type;
        $log->save();

        return redirect()->back()->with("success", "Sukses merubah data")->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $employee = Employee::withoutGlobalScopes()->find($id);
        EmployeeLog::where('employee_id', $employee->id)->delete();
        $employee->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }

}
