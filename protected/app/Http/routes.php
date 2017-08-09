<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::get('home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('taxonomi', 'TaxonomiController');
    Route::resource('varian', 'VarianController');
    Route::resource('employee', 'EmployeeController');
    Route::resource('print', 'PrintController');

    Route::get('transaction', [
        'as' => 'transaction.index',
        'uses' => 'TransactionController@index',
    ]);
    Route::get('transaction/setor', [
        'as' => 'transaction.setor',
        'uses' => 'TransactionController@setor',
    ]);
    Route::get('transaction/bon', [
        'as' => 'transaction.bon',
        'uses' => 'TransactionController@bon',
    ]);
    Route::get('transaction/detail', [
        'as' => 'transaction.detail',
        'uses' => 'TransactionController@show',
    ]);
    Route::post('transaction/rekap', [
        'as' => 'transaction.rekap',
        'uses' => 'TransactionController@rekap',
    ]);

    Route::get('transaction/setor/edit', [
        'as' => 'transaction.setor.edit',
        'uses' => 'TransactionController@editSetor',
    ]);
    Route::get('transaction/bon/edit', [
        'as' => 'transaction.bon.edit',
        'uses' => 'TransactionController@editBon',
    ]);

    Route::post('transaction/setor/create', [
        'as' => 'transaction.setor.create',
        'uses' => 'TransactionController@createSetor',
    ]);
    Route::post('transaction/bon/create', [
        'as' => 'transaction.bon.create',
        'uses' => 'TransactionController@createBon',
    ]);

    Route::post('transaction/setor/update', [
        'as' => 'transaction.setor.update',
        'uses' => 'TransactionController@updateSetor',
    ]);
    Route::post('transaction/bon/update', [
        'as' => 'transaction.bon.update',
        'uses' => 'TransactionController@updateBon',
    ]);
});
