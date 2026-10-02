<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Halaman utama: katalog menu dari semua tenant aktif.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        // Atur jam operasional kantin (Misal: 06:00 - 16:00 WIB)
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $openTime = \Carbon\Carbon::createFromTime(6, 0, 0, 'Asia/Jakarta');
        $closeTime = \Carbon\Carbon::createFromTime(16, 0, 0, 'Asia/Jakarta');
        
        // Cek apakah sekarang berada di dalam jam operasional
        $isOpen = $now->between($openTime, $closeTime);

        $products = Product::query()
            ->with('tenant:id,name,slug,status')
            ->whereHas('tenant', fn ($q) => $q->where('status', 'active'))
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->orderByDesc('is_featured')
            ->orderBy('tenant_id')
            ->orderBy('name')
            ->get();

        return view('home', compact('products', 'isOpen', 'openTime', 'closeTime'));
    }
}
