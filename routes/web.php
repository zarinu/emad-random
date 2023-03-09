<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'prefix' => '/excel',
], function () {
    Route::get('/', 'App\Http\Controllers\ExcelController@index');

    Route::post('/import', 'App\Http\Controllers\ExcelController@importData')->name('import');

    Route::get('/export', 'App\Http\Controllers\ExcelController@exportData');
});
