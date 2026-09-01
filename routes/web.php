<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Kasir\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (kelola barang & stok/warehouse)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('products', ProductController::class);

        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('/stock/create', [StockController::class, 'create'])->name('stock.create');
        Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Kasir Routes (POS & riwayat transaksi) - admin juga boleh akses
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,kasir')->group(function () {
        Route::get('/pos', [TransactionController::class, 'create'])->name('pos.index');
        Route::post('/pos', [TransactionController::class, 'store'])->name('pos.store');
        Route::get('/pos/receipt/{transaction}', [TransactionController::class, 'receipt'])->name('pos.receipt');
        Route::get('/transactions', [TransactionController::class, 'history'])->name('transactions.index');
    });

});
