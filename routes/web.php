<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Publik (Tanpa Login) ─────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Keranjang Belanja & Checkout ─────────────────────────────────────────
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// ─── Voucher Promo ────────────────────────────────────────────────────────
Route::post('/voucher/claim', [\App\Http\Controllers\VoucherController::class, 'claim'])->name('voucher.claim');
Route::get('/voucher/active', [\App\Http\Controllers\VoucherController::class, 'active'])->name('voucher.active');

// ─── Pesanan Saya ──────────────────────────────────────────────────────────
Route::get('/pesanan-saya', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('orders.index');

// ─── Dashboard Tenant / Admin (Memerlukan Login) ──────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::patch('/dashboard/orders/{order}/status', [\App\Http\Controllers\DashboardController::class, 'updateStatus'])->name('dashboard.orders.status');
    Route::patch('/dashboard/products/{product}/quick-update', [\App\Http\Controllers\ProductController::class, 'quickUpdate'])->name('dashboard.products.quick-update');
    Route::resource('/dashboard/products', \App\Http\Controllers\ProductController::class);

    // ─── Manajemen Stok Bahan & Produk ─────────────────────────────────────────
    Route::get('/dashboard/stok', [\App\Http\Controllers\IngredientController::class, 'index'])->name('dashboard.stock.index');
    Route::post('/dashboard/ingredients', [\App\Http\Controllers\IngredientController::class, 'store'])->name('dashboard.ingredients.store');
    Route::patch('/dashboard/ingredients/{ingredient}/adjust', [\App\Http\Controllers\IngredientController::class, 'adjust'])->name('dashboard.ingredients.adjust');
    Route::put('/dashboard/ingredients/{ingredient}', [\App\Http\Controllers\IngredientController::class, 'update'])->name('dashboard.ingredients.update');
    Route::delete('/dashboard/ingredients/{ingredient}', [\App\Http\Controllers\IngredientController::class, 'destroy'])->name('dashboard.ingredients.destroy');
});

// ─── Autentikasi ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [\App\Http\Controllers\AuthController::class, 'login']);
    // Pendaftaran dinonaktifkan: diarahkan ke login
    Route::get('/daftar', fn () => redirect()->route('login'))->name('register');
});

Route::post('/keluar', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/profil', fn () => redirect()->route('home'))->name('profile.edit');
