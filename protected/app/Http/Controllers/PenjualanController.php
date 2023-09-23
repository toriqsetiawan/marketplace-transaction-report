<?php

namespace App\Http\Controllers;

use App\Entities\Employee;
use App\Entities\Hutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
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

        return view('penjualan.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $employee = Employee::find($request->employee);

        return view('hutang.create')
            ->with('employee', $employee);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee' => 'required|exists:employee,id',
            'nama' => 'required|max:255',
            'harga' => 'required|numeric',
            'angsuran' => 'required|int',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Hutang::create([
            'employee_id' => $request->employee,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'angsuran' => $request->angsuran,
            'status' => 'aktif',
        ]);

        return redirect()->back()
            ->with("success", "Sukses menambah data")
            ->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            abort('404');
        }

        $data = Hutang::with(['employee'])
            ->where('employee_id', $employee->id)
            ->orderBy('created_at')
            ->paginate(10);

        return view('hutang.detail')
            ->with('employee', $employee)
            ->with('data', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Hutang::find($id);

        $employee = Employee::find($data->employee_id);

        return view('hutang.update')
            ->with('employee', $employee)
            ->with('data', $data);
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
            'employee' => 'required|exists:employee,id',
            'nama' => 'required|max:255',
            'harga' => 'required|numeric',
            'angsuran' => 'required|int',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $hutang = Hutang::find($id);

        $hutang->nama = $request->nama;
        $hutang->harga = $request->harga;
        $hutang->angsuran = $request->angsuran;
        $hutang->save();

        return redirect()->back()
            ->with("success", "Sukses merubah data")
            ->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {

    }

}
