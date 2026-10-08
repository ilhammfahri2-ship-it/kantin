<?php

namespace App\Http\Controllers;

use App\Services\CanteenSchedule;
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
        $schedule = CanteenSchedule::getStatus();
        $isOpen = $schedule['isOpen'];

        $activeVoucher = session('active_voucher');
        if (!$activeVoucher && auth()->check()) {
            $claim = \App\Models\VoucherClaim::with('voucher')
                ->where('user_id', auth()->id())
                ->where('is_used', false)
                ->latest()
                ->first();

            if ($claim && $claim->voucher && $claim->voucher->isValid()) {
                $activeVoucher = [
                    'id' => $claim->voucher->id,
                    'code' => $claim->voucher->code,
                    'name' => $claim->voucher->name,
                    'discount_percent' => $claim->voucher->discount_percent,
                ];
                session(['active_voucher' => $activeVoucher]);
            }
        }

        return view('cart.index', compact('schedule', 'isOpen', 'activeVoucher'));
    }
}
