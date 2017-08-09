<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use App\Entities\Employee;
use App\Entities\Taxonomi;
use App\Entities\Varian;

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
        $employee = Employee::all()->count();
        $varian = Varian::all()->count();
        $taxonomi = Taxonomi::all()->count();

        return view('home')
            ->with('pegawai', $employee)
            ->with('satuan', $taxonomi)
            ->with('varian', $varian);
    }
}
