<?php namespace App\Http\Controllers;

use App\Entities\Employee;
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
        $data = Employee::orderBy('nama')->paginate(10);

        if ($request->has('type')) {
            if ($request->type == 'bulanan') {
                $data = Employee::whereGolongan('bulanan')->orderBy('nama')->paginate(10);
            } elseif ($request->type == 'mingguan') {
                $data = Employee::whereGolongan('mingguan')->orderBy('nama')->paginate(10);
            }
        } else {
            if ($request->has('search')) {
                $data = Employee::where('nama', 'like', '%' . $request->search . '%')
                    ->orderBy('nama')
                    ->paginate(10);
            } else {
                $data = Employee::orderBy('nama')->paginate(10);
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

        Employee::create($request->all());

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
        $employee = Employee::find($id);

        return view('pegawai.update')->with('data', $employee);
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::find($id);

        $employee->nama = $request->nama;
        $employee->alamat = $request->alamat;
        $employee->phone = $request->phone;
        $employee->golongan = $request->golongan;
        $employee->save();

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
        $employee = Employee::find($id);
        $employee->delete();

        return redirect()->back()->with("success", "Sukses menghapus data")->withInput();
    }

}
