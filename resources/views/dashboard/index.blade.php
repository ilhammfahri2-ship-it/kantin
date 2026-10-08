@extends('layouts.dashboard')

@section('title', 'Dashboard Kantin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboardManager()">

    {{-- ── HEADER DASHBOARD & AKSI UTAMA ─────────────────────────────────── --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-2 border shadow-2xs"
                 style="background: var(--brand-subtle); border-color: var(--brand-border); color: var(--brand);">
                <span>🍱 KantinSchool Management System</span>
                <span>•</span>
                <span>Live Sync Dapur</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-1" style="color: var(--text-primary);">
                Dashboard Pengelola Kantin
            </h1>
            <p class="text-xs sm:text-sm" style="color: var(--text-secondary);">
                Kelola antrean pesanan masuk, pantau ketersediaan stok, dan perbarui harga menu secara real-time.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Tombol Tambah Produk Baru (10% CTA Accent) --}}
            <button
                type="button"
                @click="showCreateModal = true"
                class="btn-brand text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2"
                id="btn-add-product"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ Tambah Produk Baru</span>
            </button>

            <a href="{{ route('dashboard.stock.index') }}"
               class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors"
               title="Buka Halaman Manajemen Stok Bahan & Produk">
                <span>🌾 Stok Bahan & Produk</span>
            </a>

            <a href="{{ route('home') }}"
               class="btn-secondary text-xs font-semibold py-2.5 px-3.5 rounded-xl border flex items-center gap-1.5 transition-colors"
               title="Lihat katalog toko sebagai pelanggan">
                <span>🏪 Menu Toko</span>
                <span>&nearr;</span>
            </a>
        </div>
    </div>

    {{-- ── FLASH ALERTS ─────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 rounded-xl border text-sm font-semibold flex items-center justify-between backdrop-blur-md shadow-sm"
             style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
            <div class="flex items-center gap-2.5">
                <span class="text-base">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-sm font-bold opacity-70 hover:opacity-100">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition
             class="mb-6 p-4 rounded-xl border text-xs font-medium backdrop-blur-md shadow-sm"
             style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
            <div class="font-bold mb-1">Terjadi kesalahan pada formulir:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── 4 KPI STATISTIK UTAMA (60% SURFACES WITH ELEVATION) ─────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Card 1: Pesanan Aktif --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-muted);">Pesanan Aktif</h3>
                <span class="text-lg">📦</span>
            </div>
            <p class="text-3xl font-black" style="color: var(--cta-accent);">{{ $activeOrdersCount }}</p>
            <p class="text-xs mt-1 font-medium" style="color: var(--text-secondary);">
                {{ $pendingOrdersCount }} perlu konfirmasi segera
            </p>
        </div>

        {{-- Card 2: Total Pendapatan --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-muted);">Pendapatan Selesai</h3>
                <span class="text-lg">💰</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black tracking-tight" style="color: var(--brand);">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
            <p class="text-xs mt-1 font-medium" style="color: var(--text-secondary);">
                Dari {{ $completedOrdersCount }} pesanan yang selesai
            </p>
        </div>

        {{-- Card 3: Total Menu --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all cursor-pointer hover:scale-[1.01]"
             @click="activeTab = 'products'; productFilter = 'all'"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--text-muted);">Katalog Menu</h3>
                <span class="text-lg">🍱</span>
            </div>
            <p class="text-3xl font-black" style="color: var(--text-primary);">{{ $totalProductsCount }}</p>
            <p class="text-xs mt-1 font-medium hover:underline" style="color: var(--brand);">
                Klik untuk lihat semua menu &rarr;
            </p>
        </div>

        {{-- Card 4: Stok Habis / Perlu Diisi --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all cursor-pointer hover:scale-[1.01]"
             @click="activeTab = 'products'; productFilter = 'out_of_stock'"
             :style="'{{ $outOfStockCount }}' > 0 
                ? 'background-color: var(--status-danger-bg); border-color: var(--status-danger-border);' 
                : 'background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);'">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs font-bold uppercase tracking-wider"
                    style="color: {{ $outOfStockCount > 0 ? 'var(--status-danger)' : 'var(--text-muted)' }};">
                    Stok Kosong / Habis
                </h3>
                <span class="text-lg">{{ $outOfStockCount > 0 ? '⚠️' : '✅' }}</span>
            </div>
            <p class="text-3xl font-black"
               style="color: {{ $outOfStockCount > 0 ? 'var(--status-danger)' : 'var(--status-success)' }};">
                {{ $outOfStockCount }}
            </p>
            <p class="text-xs mt-1 font-bold"
               style="color: {{ $outOfStockCount > 0 ? 'var(--status-danger)' : 'var(--status-success)' }};">
                @if($outOfStockCount > 0)
                    Perlu diisi! Klik untuk ubah stok &rarr;
                @else
                    Semua menu memiliki stok aktif
                @endif
            </p>
        </div>
    </div>

    {{-- ── TAB NAVIGASI UTAMA DASHBOARD (MENYATU DENGAN GAYA WEB) ─────────── --}}
    <div class="flex items-center gap-2 mb-6 border-b pb-3 overflow-x-auto scrollbar-none" style="border-color: var(--border-default);">
        {{-- Tab 1: Pesanan Masuk --}}
        <button
            type="button"
            @click="activeTab = 'orders'"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap"
            :class="activeTab === 'orders' ? 'category-tab-active shadow-sm' : 'category-tab-inactive'"
        >
            <span class="text-base">📦</span>
            <span>Antrean Pesanan</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  :style="activeTab === 'orders' ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: var(--bg-surface-3); color: var(--text-secondary);'">
                {{ $orders->count() }}
            </span>
        </button>

        {{-- Tab 2: Daftar Produk & Menu --}}
        <button
            type="button"
            @click="activeTab = 'products'"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap"
            :class="activeTab === 'products' ? 'category-tab-active shadow-sm' : 'category-tab-inactive'"
            id="tab-btn-products"
        >
            <span class="text-base">🍽️</span>
            <span>Daftar Produk & Menu</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  :style="activeTab === 'products' ? 'background: rgba(255,255,255,0.25); color: #ffffff;' : 'background: var(--bg-surface-3); color: var(--text-secondary);'">
                {{ $products->count() }}
            </span>

            @if($outOfStockCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase animate-pulse shadow-sm"
                      style="background-color: var(--status-danger); color: #ffffff;">
                    {{ $outOfStockCount }} Kosong
                </span>
            @endif
        </button>

        {{-- Tab 3: Manajemen Stok Bahan & Produk --}}
        <a
            href="{{ route('dashboard.stock.index') }}"
            class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-2 transition-all cursor-pointer category-tab-inactive whitespace-nowrap"
            id="tab-btn-stock-management"
            title="Buka halaman penuh Manajemen Stok Bahan & Produk"
        >
            <span class="text-base">🌾</span>
            <span>Stok Dapur & Bahan</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold"
                  style="background: var(--bg-surface-3); color: var(--text-secondary);">
                {{ $ingredients->count() }} Bahan
            </span>

            @if(($outOfStockIngredientsCount + $lowStockIngredientsCount) > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase animate-pulse shadow-sm"
                      style="background-color: var(--status-warning); color: #ffffff;">
                    {{ $outOfStockIngredientsCount + $lowStockIngredientsCount }} Perlu Belanja
                </span>
            @endif
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB KONTEN 1: ANTREAN PESANAN
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'orders'" x-transition:enter="transition ease-out duration-150">
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2"
                 style="border-color: var(--border-default);">
                <div>
                    <h2 class="font-bold text-base tracking-tight" style="color: var(--text-primary);">Daftar Antrean Pesanan</h2>
                    <p class="text-xs" style="color: var(--text-muted);">Perbarui status saat pesanan mulai dimasak hingga siap diambil.</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full border self-start sm:self-auto"
                      style="background-color: var(--bg-surface-2); color: var(--text-muted); border-color: var(--border-default);">
                    Auto-refresh: 15 detik
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs font-bold uppercase tracking-wider"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);">
                            <th class="px-6 py-4">Pesanan</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Detail Menu</th>
                            <th class="px-6 py-4">Pembayaran</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-default);">
                        @forelse($orders as $order)
                            <tr class="transition-colors hover:opacity-95" style="border-color: var(--border-default);">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-mono text-xs font-bold" style="color: var(--brand);">{{ $order->order_number }}</p>
                                    <p class="text-xs mt-1" style="color: var(--text-muted);">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-bold" style="color: var(--text-primary);">{{ $order->customer_name ?? '-' }}</p>
                                    <p class="text-xs mt-1" style="color: var(--text-muted);">Kelas: {{ $order->customer_class ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">
                                    <div class="space-y-1">
                                        @foreach($order->items as $item)
                                            <div class="flex items-start gap-2">
                                                <span class="font-bold text-xs" style="color: var(--text-primary);">{{ $item->quantity }}x</span>
                                                <span class="text-xs">{{ $item->product_name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($order->notes)
                                        <p class="mt-2 text-xs italic p-2 rounded-lg border"
                                           style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);">
                                            "{{ $order->notes }}"
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-bold" style="color: var(--text-primary);">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                    <div class="mt-1">
                                        @if($order->payment_method === 'qris')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border"
                                                  style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);">
                                                QRIS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border"
                                                  style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                                                CASH
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($order->status === 'pending')
                                        <span class="status-pill status-pill-pending">Menunggu Konfirmasi</span>
                                    @elseif($order->status === 'confirmed' || $order->status === 'preparing')
                                        <span class="status-pill status-pill-processing">Sedang Dimasak</span>
                                    @elseif($order->status === 'ready')
                                        <span class="status-pill status-pill-ready">Siap Diambil</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="status-pill status-pill-cancelled">Dibatalkan</span>
                                    @else
                                        <span class="status-pill status-pill-completed">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                        @php
                                            $nextStatus = '';
                                            $btnLabel = '';
                                            
                                            if ($order->status === 'pending') {
                                                $nextStatus = 'confirmed';
                                                $btnLabel = 'Konfirmasi Pesanan';
                                            } elseif ($order->status === 'confirmed') {
                                                $nextStatus = 'preparing';
                                                $btnLabel = 'Mulai Masak';
                                            } elseif ($order->status === 'preparing') {
                                                $nextStatus = 'ready';
                                                $btnLabel = 'Siap Diambil';
                                            } elseif ($order->status === 'ready') {
                                                $nextStatus = 'completed';
                                                $btnLabel = 'Selesai ✔';
                                            }
                                        @endphp

                                        <div class="flex flex-col gap-2 items-end">
                                            @if($nextStatus)
                                                <form action="{{ route('dashboard.orders.status', $order->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                    <button type="submit" class="btn-brand text-xs px-3 py-1.5 rounded-lg shadow-sm">
                                                        {{ $btnLabel }} &rarr;
                                                    </button>
                                                </form>
                                            @endif

                                            @if($order->status === 'pending')
                                                <form action="{{ route('dashboard.orders.status', $order->id) }}" method="POST"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menolak pesanan ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="text-[11px] font-medium transition-opacity hover:opacity-80"
                                                            style="color: var(--status-danger);">
                                                        Tolak Pesanan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs" style="color: var(--text-muted);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full border mb-3"
                                         style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                                        <span class="text-xl">📭</span>
                                    </div>
                                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Belum Ada Pesanan Masuk</h3>
                                    <p class="text-xs mt-1" style="color: var(--text-muted);">Pesanan dari pelanggan akan otomatis muncul di sini secara real-time.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         TAB KONTEN 2: DAFTAR PRODUK & MENU (UBAH STOK & UPDATE HARGA LANGSUNG)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'products'" x-transition:enter="transition ease-out duration-150">
        
        {{-- Baris Filter & Pencarian Menu --}}
        <div class="mb-5 flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            {{-- Search Box Menu --}}
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                     style="color: var(--text-muted);"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="search"
                    x-model="productSearch"
                    placeholder="Cari menu makanan, minuman, tenant..."
                    class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-xl border outline-none transition-colors"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                    aria-label="Cari menu kantin"
                >
            </div>

            {{-- Filter Kategori & Status Stok --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                <button
                    type="button"
                    @click="productFilter = 'all'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all"
                    :class="productFilter === 'all' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Semua ({{ $products->count() }})
                </button>

                <button
                    type="button"
                    @click="productFilter = 'out_of_stock'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all flex items-center gap-1"
                    :class="productFilter === 'out_of_stock' ? 'category-tab-active' : 'btn-secondary'"
                    :style="productFilter !== 'out_of_stock' && {{ $outOfStockCount }} > 0 ? 'border-color: var(--status-danger); color: var(--status-danger);' : ''"
                >
                    <span>🔴 Stok Habis</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black"
                          :style="productFilter === 'out_of_stock' ? 'background: #fff; color: #000;' : 'background: var(--status-danger); color: #fff;'">
                        {{ $outOfStockCount }}
                    </span>
                </button>

                <button
                    type="button"
                    @click="productFilter = 'low_stock'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all"
                    :class="productFilter === 'low_stock' ? 'category-tab-active' : 'btn-secondary'"
                >
                    ⚠️ Menipis ({{ $lowStockCount }})
                </button>

                <button
                    type="button"
                    @click="productFilter = 'makanan_berat'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="productFilter === 'makanan_berat' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Makanan Berat
                </button>

                <button
                    type="button"
                    @click="productFilter = 'minuman'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="productFilter === 'minuman' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Minuman
                </button>

                <button
                    type="button"
                    @click="productFilter = 'makanan_ringan'"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all"
                    :class="productFilter === 'makanan_ringan' ? 'category-tab-active' : 'btn-secondary'"
                >
                    Camilan
                </button>
            </div>
        </div>

        {{-- Tabel Produk & Menu --}}
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
            
            <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                 style="border-color: var(--border-default);">
                <div>
                    <h2 class="font-bold text-base tracking-tight" style="color: var(--text-primary);">
                        Katalog Menu & Pengaturan Stok
                    </h2>
                    <p class="text-xs" style="color: var(--text-muted);">
                        Anda dapat mengubah stok yang kosong atau mengupdate harga langsung di kolom tabel bawah ini.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showCreateModal = true"
                    class="btn-brand text-xs font-bold py-2 px-3.5 rounded-xl self-start sm:self-auto flex items-center gap-1.5"
                >
                    <span>+ Tambah Menu Baru</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b text-xs font-bold uppercase tracking-wider"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);">
                            <th class="px-6 py-3.5">Menu / Produk</th>
                            <th class="px-6 py-3.5">Kategori</th>
                            <th class="px-6 py-3.5">Harga Satuan (Rp)</th>
                            <th class="px-6 py-3.5">Stok Porsi</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-default);">
                        @forelse($products as $product)
                            <tr
                                x-show="filterProduct({
                                    id: {{ $product->id }},
                                    name: '{{ addslashes($product->name) }}',
                                    tenant: '{{ addslashes($product->tenant->name ?? '') }}',
                                    category: '{{ $product->category }}',
                                    stock: {{ $product->stock }},
                                    price: {{ $product->price }}
                                })"
                                class="transition-colors hover:opacity-95"
                                style="border-color: var(--border-default);"
                                id="row-product-{{ $product->id }}"
                            >
                                {{-- Kolom 1: Info Produk & Gambar --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3.5">
                                        @if($product->image)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                 class="w-12 h-12 rounded-xl object-cover border shrink-0"
                                                 style="border-color: var(--border-default);">
                                        @else
                                            <div class="w-12 h-12 rounded-xl border flex items-center justify-center text-lg shrink-0"
                                                 style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                                                🍱
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-sm truncate" style="color: var(--text-primary);">
                                                {{ $product->name }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-xs font-semibold" style="color: var(--brand);">
                                                    {{ $product->tenant->name ?? 'Tenant' }}
                                                </span>
                                                @if($product->is_featured)
                                                    <span class="text-[10px] font-extrabold px-1.5 py-0.2 rounded-full uppercase"
                                                          style="background: var(--brand-subtle); color: var(--brand); border: 1px solid var(--brand-border);">
                                                        ★ Favorit
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom 2: Kategori --}}
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-medium" style="color: var(--text-secondary);">
                                    <span class="px-2.5 py-1 rounded-lg border"
                                          style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                                        {{ $product->category_label }}
                                    </span>
                                </td>

                                {{-- Kolom 3: Update Harga Langsung --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST"
                                          class="flex items-center gap-1.5"
                                          @submit.prevent="submitQuickUpdate($event)">
                                        @csrf
                                        @method('PATCH')
                                        <div class="relative flex items-center">
                                            <span class="absolute left-2.5 text-xs font-bold" style="color: var(--text-muted);">Rp</span>
                                            <input
                                                type="number"
                                                name="price"
                                                value="{{ (int) $product->price }}"
                                                min="0"
                                                step="500"
                                                required
                                                class="w-28 pl-8 pr-2 py-1.5 text-xs font-extrabold rounded-lg border outline-none focus:ring-1"
                                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                                                title="Ubah harga produk ini"
                                            >
                                        </div>
                                        <button
                                            type="submit"
                                            class="btn-secondary text-[11px] font-bold px-2 py-1.5 rounded-lg border transition-all"
                                            title="Simpan harga baru"
                                        >
                                            Update
                                        </button>
                                    </form>
                                </td>

                                {{-- Kolom 4: Ubah Stok yang Kosong Langsung --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST"
                                          class="flex items-center gap-1.5"
                                          @submit.prevent="submitQuickUpdate($event)">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <div class="flex items-center">
                                            <input
                                                type="number"
                                                name="stock"
                                                value="{{ $product->stock }}"
                                                min="0"
                                                required
                                                class="w-20 px-2.5 py-1.5 text-xs font-black text-center rounded-lg border outline-none focus:ring-1"
                                                :style="'{{ $product->stock }}' <= 0 
                                                    ? 'background-color: #FEF2F2; color: #DC2626; border-color: #FECACA;' 
                                                    : 'background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);'"
                                                title="Ubah jumlah stok tersedia"
                                            >
                                        </div>

                                        <button
                                            type="submit"
                                            class="text-[11px] font-bold px-2.5 py-1.5 rounded-lg border transition-all"
                                            :class="'{{ $product->stock }}' <= 0 ? 'btn-brand shadow-sm' : 'btn-secondary'"
                                            title="Simpan stok baru"
                                        >
                                            @if($product->stock <= 0)
                                                Isi Stok!
                                            @else
                                                Simpan
                                            @endif
                                        </button>
                                    </form>

                                    @if($product->stock <= 0)
                                        <span class="inline-block mt-1 text-[10px] font-extrabold uppercase tracking-wider" style="color: var(--status-danger);">
                                            ● Stok Kosong
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider" style="color: var(--status-warning);">
                                            ● Menipis ({{ $product->stock }})
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom 5: Status Ketersediaan --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->stock <= 0)
                                        <span class="status-pill status-pill-cancelled">Habis (0)</span>
                                    @elseif($product->is_available)
                                        <span class="status-pill status-pill-ready">Tersedia</span>
                                    @else
                                        <span class="status-pill status-pill-completed">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- Kolom 6: Aksi Edit Lengkap & Hapus --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2.5 text-xs font-semibold">
                                        {{-- Tombol Edit Lengkap (buka modal quick edit / ke halaman edit) --}}
                                        <button
                                            type="button"
                                            @click="openQuickEdit({
                                                id: {{ $product->id }},
                                                name: '{{ addslashes($product->name) }}',
                                                price: {{ $product->price }},
                                                stock: {{ $product->stock }},
                                                is_available: {{ $product->is_available ? 'true' : 'false' }}
                                            })"
                                            class="px-2.5 py-1.5 rounded-lg border btn-secondary transition-opacity hover:opacity-80"
                                            style="color: var(--brand);"
                                            title="Edit lengkap harga, stok & status"
                                        >
                                            ✏️ Edit
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ addslashes($product->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="from_dashboard" value="1">
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg transition-colors hover:opacity-80"
                                                    style="color: var(--status-danger);"
                                                    title="Hapus Menu">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-3xl mb-2">🍽️</div>
                                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Belum Ada Produk di Menu Kantin</h3>
                                    <p class="text-xs mt-1 mb-4" style="color: var(--text-muted);">Mulai tambahkan hidangan makanan dan minuman pertama Anda.</p>
                                    <button
                                        type="button"
                                        @click="showCreateModal = true"
                                        class="btn-brand text-xs font-bold px-4 py-2 rounded-xl"
                                    >
                                        + Tambah Menu Sekarang
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Info Jumlah Menu yang Tampil --}}
            <div class="px-6 py-3.5 border-t flex items-center justify-between text-xs"
                 style="border-color: var(--border-default); color: var(--text-muted);">
                <span>Total: <b>{{ $products->count() }}</b> menu terdaftar di kantin.</span>
                <span>Klik <b>Update/Simpan</b> untuk menerapkan perubahan seketika.</span>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL: TAMBAH PRODUK BARU (LANGSUNG DARI DASHBOARD)
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showCreateModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <div
            x-show="showCreateModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showCreateModal = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
        ></div>

        {{-- Modal Dialog --}}
        <div
            x-show="showCreateModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-xl rounded-2xl border p-6 sm:p-7 shadow-2xl z-10 overflow-hidden"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-4 border-b mb-5" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-lg font-bold" style="color: var(--text-primary);">Tambah Produk / Menu Baru</h3>
                    <p class="text-xs" style="color: var(--text-muted);">Menu baru akan langsung muncul di katalog pembeli.</p>
                </div>
                <button
                    type="button"
                    @click="showCreateModal = false"
                    class="p-2 rounded-lg transition-colors hover:bg-stone-200/50"
                    style="color: var(--text-secondary);"
                >
                    &times;
                </button>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="from_dashboard" value="1">

                <div>
                    <label for="modal-name" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Nama Menu Makanan / Minuman <span style="color: var(--status-danger);">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="modal-name"
                        required
                        placeholder="Contoh: Ayam Geprek Sambal Matah"
                        class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm border outline-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="modal-category" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Kategori <span style="color: var(--status-danger);">*</span>
                        </label>
                        <select
                            name="category"
                            id="modal-category"
                            required
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                            <option value="makanan_berat">Makanan Berat</option>
                            <option value="makanan_ringan">Makanan Ringan (Camilan)</option>
                            <option value="minuman">Minuman Segar</option>
                            <option value="dessert">Dessert / Penutup</option>
                        </select>
                    </div>

                    @if(auth()->user()->isAdmin() && isset($tenants) && $tenants->count() > 1)
                    <div>
                        <label for="modal-tenant" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Pilih Tenant Kantin <span style="color: var(--status-danger);">*</span>
                        </label>
                        <select
                            name="tenant_id"
                            id="modal-tenant"
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label for="modal-price" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Harga Satuan (Rp) <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input
                            type="number"
                            name="price"
                            id="modal-price"
                            min="0"
                            step="500"
                            required
                            placeholder="15000"
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-bold border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                    </div>

                    <div>
                        <label for="modal-stock" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                            Stok Porsi Awal <span style="color: var(--status-danger);">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            id="modal-stock"
                            min="0"
                            required
                            value="20"
                            class="w-full rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-bold border outline-none"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                    </div>
                </div>

                <div>
                    <label for="modal-image" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Foto Makanan (Opsional)
                    </label>
                    <input
                        type="file"
                        name="image"
                        id="modal-image"
                        accept="image/*"
                        class="w-full rounded-xl px-3 py-2 text-xs border outline-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);"
                    >
                </div>

                <div>
                    <label for="modal-description" class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea
                        name="description"
                        id="modal-description"
                        rows="2"
                        placeholder="Rasa gurih pedas, porsi kenyang lengkap sambal dan lalapan..."
                        class="w-full rounded-xl px-3.5 py-2 text-xs sm:text-sm border outline-none resize-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    ></textarea>
                </div>

                <div class="flex items-center gap-6 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_available" value="1" checked style="accent-color: var(--brand);" class="rounded w-4 h-4">
                        <span class="text-xs font-semibold" style="color: var(--text-primary);">Langsung Tersedia (Bisa Dipesan)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" style="accent-color: var(--brand);" class="rounded w-4 h-4">
                        <span class="text-xs font-semibold" style="color: var(--text-primary);">★ Menu Unggulan</span>
                    </label>
                </div>

                <div class="pt-5 border-t flex justify-end gap-3" style="border-color: var(--border-default);">
                    <button
                        type="button"
                        @click="showCreateModal = false"
                        class="btn-secondary px-4 py-2 text-xs font-semibold rounded-xl"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="btn-brand px-5 py-2 text-xs font-extrabold rounded-xl shadow-md"
                    >
                        Simpan & Tambahkan ke Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         MODAL: EDIT CEPAT STOK & HARGA
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="showQuickEditModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
    >
        <div
            x-show="showQuickEditModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showQuickEditModal = false"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"
        ></div>

        <div
            x-show="showQuickEditModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-md rounded-2xl border p-6 shadow-2xl z-10"
            style="background-color: var(--bg-surface); border-color: var(--border-default);"
        >
            <div class="flex items-center justify-between pb-3 border-b mb-4" style="border-color: var(--border-default);">
                <div>
                    <h3 class="text-base font-bold" style="color: var(--text-primary);">Update Stok & Harga Menu</h3>
                    <p class="text-xs truncate max-w-xs" style="color: var(--brand);" x-text="editingProduct.name"></p>
                </div>
                <button type="button" @click="showQuickEditModal = false" class="text-base font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/dashboard/products') }}/' + editingProduct.id + '/quick-update'" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Harga Baru (Rp)</label>
                    <input
                        type="number"
                        name="price"
                        x-model="editingProduct.price"
                        min="0"
                        step="500"
                        required
                        class="w-full rounded-xl px-3.5 py-2.5 text-sm font-extrabold border outline-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color: var(--text-secondary);">Stok Porsi Tersedia</label>
                    <input
                        type="number"
                        name="stock"
                        x-model="editingProduct.stock"
                        min="0"
                        required
                        class="w-full rounded-xl px-3.5 py-2.5 text-sm font-black border outline-none"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    >
                    <p class="text-[11px] mt-1" style="color: var(--text-muted);">
                        Jika diisi di atas 0, menu otomatis aktif kembali dan dapat dipesan oleh pelanggan.
                    </p>
                </div>

                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_available"
                            value="1"
                            x-model="editingProduct.is_available"
                            style="accent-color: var(--brand);"
                            class="rounded w-4 h-4"
                        >
                        <span class="text-xs font-semibold" style="color: var(--text-primary);">Status: Buka / Bisa Dipesan</span>
                    </label>
                </div>

                <div class="pt-4 border-t flex justify-end gap-2.5" style="border-color: var(--border-default);">
                    <button
                        type="button"
                        @click="showQuickEditModal = false"
                        class="btn-secondary px-4 py-2 text-xs font-medium rounded-xl"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="btn-brand px-5 py-2 text-xs font-bold rounded-xl shadow-md"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function dashboardManager() {
    return {
        activeTab: '{{ request('tab', ($outOfStockCount > 0 && request('from_stock_alert')) ? 'products' : request('tab', 'orders')) }}',
        productSearch: '',
        productFilter: '{{ request('filter', 'all') }}',
        showCreateModal: false,
        showQuickEditModal: false,
        editingProduct: {
            id: null,
            name: '',
            price: 0,
            stock: 0,
            is_available: true
        },

        openQuickEdit(product) {
            this.editingProduct = {
                id: product.id,
                name: product.name,
                price: product.price,
                stock: product.stock,
                is_available: Boolean(product.is_available)
            };
            this.showQuickEditModal = true;
        },

        filterProduct(item) {
            const query = this.productSearch.toLowerCase().trim();
            const matchesSearch = !query || 
                item.name.toLowerCase().includes(query) || 
                item.tenant.toLowerCase().includes(query);

            let matchesCategory = true;
            if (this.productFilter === 'out_of_stock') {
                matchesCategory = (item.stock <= 0);
            } else if (this.productFilter === 'low_stock') {
                matchesCategory = (item.stock > 0 && item.stock <= 5);
            } else if (this.productFilter !== 'all') {
                matchesCategory = (item.category === this.productFilter);
            }

            return matchesSearch && matchesCategory;
        },

        async submitQuickUpdate(event) {
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = '...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    submitBtn.innerText = '✓';
                    setTimeout(() => {
                        submitBtn.innerText = originalText;
                        submitBtn.disabled = false;
                        // Reload data smoothly or keep state
                        window.location.href = '{{ route('dashboard.index', ['tab' => 'products']) }}';
                    }, 400);
                } else {
                    form.submit(); // fallback to standard submit
                }
            } catch (err) {
                form.submit();
            }
        },

        init() {
            // Auto-Refresh Setup: HANYA saat di Tab Pesanan dan tidak sedang mengetik atau membuka modal
            setInterval(() => {
                const hasOpenModal = this.showCreateModal || this.showQuickEditModal;
                const isTyping = document.querySelector('input:focus, textarea:focus, select:focus');
                
                if (this.activeTab === 'orders' && !hasOpenModal && !isTyping) {
                    window.location.reload();
                }
            }, 15000);

            // Notifikasi Audio saat pesanan baru masuk
            const currentPending = {{ $pendingOrdersCount ?? 0 }};
            let previousPending = sessionStorage.getItem('ekantin_pending_orders');
            
            if (previousPending === null) {
                sessionStorage.setItem('ekantin_pending_orders', currentPending);
            } else {
                previousPending = parseInt(previousPending);
                if (currentPending > previousPending) {
                    try {
                        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3'); 
                        audio.play().catch(() => {});
                    } catch(e) {}
                }
                sessionStorage.setItem('ekantin_pending_orders', currentPending);
            }
        }
    };
}
</script>
@endpush
@endsection
