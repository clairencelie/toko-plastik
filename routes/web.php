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
    return redirect()->route('barang.index');
});

Route::resource('barang', \App\Http\Controllers\BarangController::class);
Route::resource('pelanggan', \App\Http\Controllers\PelangganController::class);
Route::resource('supplier', \App\Http\Controllers\SupplierController::class);
Route::resource('penerimaan', \App\Http\Controllers\PenerimaanController::class);
Route::resource('penjualan', \App\Http\Controllers\PenjualanController::class);
Route::get('/penjualan/{penjualan}/print', [\App\Http\Controllers\PenjualanController::class, 'printInvoice'])->name('penjualan.print');

Route::resource('adjustmen', \App\Http\Controllers\AdjustmenController::class);
Route::resource('tagihan', \App\Http\Controllers\TagihanController::class);
Route::get('/tagihan/ar/{customerId}', [\App\Http\Controllers\TagihanController::class, 'getUnpaidAr']);
Route::get('/tagihan/{tagihan}/print', [\App\Http\Controllers\TagihanController::class, 'print'])->name('tagihan.print');

// Keuangan / Pelunasan
Route::resource('kasmasuk', \App\Http\Controllers\KasmasukController::class);
Route::get('/kasmasuk/ar/{customerId}', [\App\Http\Controllers\KasmasukController::class, 'getUnpaidAr']);
Route::resource('kaskeluar', \App\Http\Controllers\KaskeluarController::class);
Route::get('/kaskeluar/ap/{supplierId}', [\App\Http\Controllers\KaskeluarController::class, 'getUnpaidAp']);

Route::get('/report/stock', [\App\Http\Controllers\ReportController::class, 'stockReport'])->name('report.stock');
Route::get('/report/financial', [\App\Http\Controllers\ReportController::class, 'financialReport'])->name('report.financial');
