<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Klaim voucher promo via AJAX / Fetch API.
     */
    public function claim(Request $request): JsonResponse
    {
        // 1. Validasi Autentikasi Pengguna
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'login_url' => route('login'),
                'message' => 'Silakan masuk ke akun Anda terlebih dahulu untuk mengklaim voucher diskon ini.'
            ], 401);
        }

        $code = strtoupper(trim($request->input('code', 'KANTINHEMAT')));

        // 2. Cek Keberadaan Voucher di Database
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher || !$voucher->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher promo tidak ditemukan atau sedang tidak aktif.'
            ], 404);
        }

        // 3. Validasi Masa Kedaluwarsa Voucher (Batas Waktu)
        if ($voucher->isExpired()) {
            return response()->json([
                'success' => false,
                'expired' => true,
                'message' => 'Maaf, voucher ' . $voucher->code . ' telah kedaluwarsa pada ' . $voucher->expires_at->format('d/m/Y H:i') . ' WIB.'
            ], 422);
        }

        // 4. Validasi Aturan Bisnis: Satu Akun Hanya Bisa Mengklaim Satu Kali
        $existingClaim = VoucherClaim::where('voucher_id', $voucher->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingClaim) {
            if ($existingClaim->is_used) {
                return response()->json([
                    'success' => false,
                    'already_claimed' => true,
                    'is_used' => true,
                    'message' => 'Anda sudah pernah mengklaim dan menggunakan voucher ' . $voucher->code . '. Promo ini dibatasi 1 kali klaim per akun.'
                ], 422);
            }

            // Voucher sudah diklaim dan belum terpakai -> aktifkan kembali di sesi
            session(['active_voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_percent' => $voucher->discount_percent,
            ]]);

            return response()->json([
                'success' => false,
                'already_claimed' => true,
                'is_used' => false,
                'message' => 'Anda sudah mengklaim voucher ' . $voucher->code . ' sebelumnya. Diskon ' . $voucher->discount_percent . '% sudah aktif untuk pesanan Anda.',
                'voucher' => [
                    'code' => $voucher->code,
                    'discount_percent' => $voucher->discount_percent,
                ]
            ], 422);
        }

        // 5. Simpan Data Klaim ke Database (voucher_claims)
        VoucherClaim::create([
            'voucher_id' => $voucher->id,
            'user_id' => auth()->id(),
            'claimed_at' => now(),
            'is_used' => false,
        ]);

        // 6. Terapkan Pemotongan Diskon ke Sesi Keranjang / Pesanan yang Sedang Berlangsung
        session(['active_voucher' => [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'discount_percent' => $voucher->discount_percent,
        ]]);

        return response()->json([
            'success' => true,
            'message' => 'Selamat! Diskon ' . $voucher->discount_percent . '% dari voucher ' . $voucher->code . ' berhasil diaktifkan untuk pesanan Anda!',
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'discount_percent' => $voucher->discount_percent,
                'expires_at' => $voucher->expires_at->format('d/m/Y'),
            ]
        ]);
    }

    /**
     * Dapatkan informasi voucher yang sedang aktif di sesi pengguna.
     */
    public function active(): JsonResponse
    {
        $activeVoucher = session('active_voucher');

        if (!$activeVoucher && auth()->check()) {
            $claim = VoucherClaim::with('voucher')
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

        return response()->json([
            'active_voucher' => $activeVoucher,
        ]);
    }
}
