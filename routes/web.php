<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Publik (Tanpa Login) ─────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Keranjang Belanja ─────────────────────────────────────────────────────
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// ─── Pesanan Saya ──────────────────────────────────────────────────────────
Route::get('/pesanan-saya', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('orders.index');

// ─── Dashboard Tenant / Admin (Memerlukan Login) ──────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::patch('/dashboard/orders/{order}/status', [\App\Http\Controllers\DashboardController::class, 'updateStatus'])->name('dashboard.orders.status');
    Route::resource('/dashboard/products', \App\Http\Controllers\ProductController::class);
});

// ─── Autentikasi ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/daftar', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [\App\Http\Controllers\AuthController::class, 'register']);
});

Route::post('/keluar', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/profil', fn () => redirect()->route('home'))->name('profile.edit');
