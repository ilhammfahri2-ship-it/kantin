<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * CartController — Mengelola halaman keranjang belanja.
 * 
 * Cart state disimpan di sessionStorage sisi klien (JavaScript).
 * Controller ini hanya menyajikan tampilan halaman cart.
 * Logika tambah/hapus item ditangani oleh JS di sisi klien (app.js).
 * Proses checkout akan mengirim data cart ke server via POST.
 */
class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang belanja.
     */
    public function index(): \Illuminate\View\View
    {
        return view('cart.index');
    }
}
