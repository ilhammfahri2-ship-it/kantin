<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ─── Halaman Utama ─────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Keranjang Belanja ─────────────────────────────────────────────────────
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');

// ─── Auth Stubs (akan diisi saat implementasi auth) ───────────────────────
// Sementara redirect ke home agar tidak error saat link diklik
Route::get('/masuk', fn () => redirect()->route('home'))->name('login');
Route::get('/daftar', fn () => redirect()->route('home'))->name('register');
Route::post('/keluar', fn () => redirect()->route('home'))->name('logout');
Route::get('/profil', fn () => redirect()->route('home'))->name('profile.edit');
