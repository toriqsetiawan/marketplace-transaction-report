<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TaxonomiController;
use App\Http\Controllers\VarianController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportPenjualanController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;

// Authentication routes
Auth::routes(['register' => false]);

// Redirect '/' to 'login'
Route::get('/', function () {
    return redirect('login');
});

// Home route
Route::get('home', [HomeController::class, 'index']);

// Authenticated routes
Route::group(['middleware' => 'auth'], function () {
    // Resource routes
    Route::resource('taxonomi', TaxonomiController::class);
    Route::resource('varian', VarianController::class);
    Route::resource('employee', EmployeeController::class);
    Route::resource('print', PrintController::class);
    Route::resource('product', ProductController::class);
    Route::resource('purchase', PurchaseController::class);
    Route::resource('penjualan', PenjualanController::class);
    Route::resource('return', ReturnController::class);
    Route::resource('user', UserController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('stock', StockController::class);

    Route::group(['middleware' => 'administrator'], function () {
        Route::resource('report-penjualan', ReportPenjualanController::class);
    });

    // Custom routes
    Route::post('transaction/recovery', [PrintController::class, 'recovery'])->name('transaction.recovery');
    Route::post('weekly-report', [PrintController::class, 'weeklyReport'])->name('weekly-report.print');

    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('transaction/setor', [TransactionController::class, 'setor'])->name('transaction.setor');
    Route::get('transaction/bon', [TransactionController::class, 'bon'])->name('transaction.bon');
    Route::get('transaction/detail', [TransactionController::class, 'show'])->name('transaction.detail');

    Route::post('transaction/rekap', [TransactionController::class, 'rekap'])->name('transaction.rekap');
    Route::get('transaction/setor/edit', [TransactionController::class, 'editSetor'])->name('transaction.setor.edit');
    Route::get('transaction/bon/edit', [TransactionController::class, 'editBon'])->name('transaction.bon.edit');

    Route::post('transaction/setor/create', [TransactionController::class, 'createSetor'])->name('transaction.setor.create');
    Route::post('transaction/bon/create', [TransactionController::class, 'createBon'])->name('transaction.bon.create');
    Route::post('transaction/setor/update', [TransactionController::class, 'updateSetor'])->name('transaction.setor.update');
    Route::post('transaction/bon/update', [TransactionController::class, 'updateBon'])->name('transaction.bon.update');

    Route::post('transaction/bon/delete', [TransactionController::class, 'destroy'])->name('transaction.bon.delete');
    Route::post('transaction/setor/delete', [TransactionController::class, 'destroy'])->name('transaction.setor.delete');
});
