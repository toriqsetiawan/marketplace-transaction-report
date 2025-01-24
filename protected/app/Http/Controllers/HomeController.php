<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Taxonomi;
use App\Models\Varian;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        if (env('ONLY_ONLINE_SHOP')) {
            return redirect('/report-penjualan');
        }

        $employee = Employee::all()->count();
        $varian = Varian::all()->count();
        $taxonomi = Taxonomi::all()->count();

        return view('home')
            ->with('pegawai', $employee)
            ->with('satuan', $taxonomi)
            ->with('varian', $varian);
    }
}
