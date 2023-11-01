<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportPenjualanController extends Controller
{
    public function index(Request $request)
    {
        return view('report-penjualan.index');
    }
}
