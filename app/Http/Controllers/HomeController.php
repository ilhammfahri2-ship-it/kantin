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
        $products = Product::query()
            ->with('tenant:id,name,slug,status') // eager load hanya kolom yang dibutuhkan
            ->whereHas('tenant', fn ($q) => $q->where('status', 'active'))
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->orderByDesc('is_featured')   // produk unggulan tampil duluan
            ->orderBy('tenant_id')
            ->orderBy('name')
            ->get();

        return view('home', compact('products'));
    }
}
