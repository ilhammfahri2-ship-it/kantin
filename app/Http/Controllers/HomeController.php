<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use App\Services\CanteenSchedule;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Halaman utama: katalog menu dari semua tenant aktif.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        // Dapatkan status jam operasional kantin (Sesi 1: 09:30 - 10:00 WIB, Sesi 2: 12:00 - 13:00 WIB)
        $schedule = CanteenSchedule::getStatus();
        $isOpen = $schedule['isOpen'];

        $products = Product::query()
            ->with('tenant:id,name,slug,status')
            ->whereHas('tenant', fn ($q) => $q->where('status', 'active'))
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->orderByDesc('is_featured')
            ->orderBy('tenant_id')
            ->orderBy('name')
            ->get();

        $voucher = \App\Models\Voucher::where('code', 'KANTINHEMAT')->first();
        $userVoucherClaim = null;
        if (auth()->check() && $voucher) {
            $userVoucherClaim = \App\Models\VoucherClaim::where('voucher_id', $voucher->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('home', compact('products', 'isOpen', 'schedule', 'voucher', 'userVoucherClaim'));
    }
}
