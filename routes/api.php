<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\RiwayatTransaksiController;

Route::get('/run-migrate', function () {
    Artisan::call('migrate --force');
    return Artisan::output();
});
Route::get('/reset-migration', function () {
    Artisan::call('migrate:fresh --force');
    return Artisan::output();
});


Route::middleware(['auth:api'])->group(function () {
    Route::get('/riwayat-transaksi', [RiwayatTransaksiController::class, 'index']);
    Route::get('/riwayat-transaksi/{id}', [RiwayatTransaksiController::class, 'show']);
});


Route::middleware('auth:api')->group(function () {
    Route::post('/checkout', [TransaksiController::class, 'checkout']);
    Route::get('/transaksi/saya', [TransaksiController::class, 'myTransactions']);
    Route::get('/transaksi', [TransaksiController::class, 'allTransactions']); // hanya admin
    Route::put('/transaksi/{id}/status', [TransaksiController::class, 'updateStatus']); // hanya admin
});


Route::middleware(['auth:api'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index']); // pelanggan
    Route::get('/keranjang-admin', [KeranjangController::class, 'all']); // admin
    Route::get('/keranjang/{id}', [KeranjangController::class, 'show']); // admin/pelanggan
    Route::post('/keranjang', [KeranjangController::class, 'store']);
    Route::put('/keranjang/{id}', [KeranjangController::class, 'update']);
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy']);
});



Route::middleware(['auth:api'])->group(function () {
    Route::get('/produk', [ProdukController::class, 'index']); // Semua user
    Route::get('/produk/{id}', [ProdukController::class, 'show']);
    Route::post('/produk', [ProdukController::class, 'store']); // Admin
    Route::put('/produk/{id}', [ProdukController::class, 'update']); // Admin
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']); // Admin
});



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
