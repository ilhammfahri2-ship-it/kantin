@extends('layouts.app')

@section('title', 'Menu Kantin')
@section('meta_description', 'Jelajahi menu makanan dan minuman dari berbagai tenant kantin. Pesan sekarang tanpa antre!')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 pb-8">

    {{-- ── 1. COMPACT HEADER & STATUS JAM ISTIRAHAT ─────────────────────── --}}
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center flex-wrap gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: var(--text-primary);">
                        Mau pesan apa hari ini?
                    </h1>

                    {{-- Status Badge Kantin: Terintegrasi Variabel Tema Kuliner --}}
                    @if($isOpen)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border shadow-2xs"
                             style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
                            <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--status-success);"></span>
                            <span>Buka • {{ $schedule['currentSession'] }}</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border shadow-2xs"
                             style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                            <span class="w-2 h-2 rounded-full" style="background-color: var(--status-danger);"></span>
                            <span>Tutup • Buka {{ $schedule['nextSession'] }}</span>
                        </div>
                    @endif
                </div>

                {{-- 3 Badge Info Disamakan: Gourmet Linen & Warm Terracotta --}}
                <div class="flex items-center flex-wrap gap-2 mt-2">
                    <div class="badge-info-pill">
                        <span class="text-xs">⏰</span>
                        <span>Jam Istirahat: <b>09:30–10:00</b> & <b>12:00–13:00 WIB</b></span>
                    </div>
                    <div class="badge-info-pill">
                        <span class="text-xs">⚡</span>
                        <span>Siap 5–10 Menit</span>
                    </div>
                    <div class="badge-info-pill">
                        <span class="text-xs">💳</span>
                        <span>Tunai & QRIS</span>
                    </div>
                </div>
            </div>

            @if(!$isOpen)
                {{-- Notifikasi Kompak Saat Tutup --}}
                <div class="px-3 py-1.5 rounded-xl border text-xs font-medium flex items-center gap-2 self-start sm:self-auto shadow-xs"
                     style="background-color: var(--status-danger-bg); border-color: var(--status-danger-border); color: var(--status-danger);">
                    <span>🛑</span>
                    <span>Pemesanan ditutup sementara (Pukul {{ $schedule['currentTime'] }} WIB)</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ── 2. CARD BANNER PROMO HORIZONTAL (COMPACT & SLIM) ─────────────── --}}
    @php
        $heroFeatured = $products->firstWhere('is_featured', true) ?? $products->first();
    @endphp
    <div class="relative overflow-hidden rounded-xl border mb-4 banner-luxury p-3 sm:px-4 sm:py-2.5 shadow-xs"
         style="border-color: var(--border-default);">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-2.5">
            {{-- Bagian Teks Promo Ringkas --}}
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="px-2 py-0.5 rounded-md text-[0.68rem] font-bold uppercase tracking-wider shrink-0 shadow-2xs"
                      style="background-color: var(--brand); color: #ffffff;">
                    ✦ Promo
                </span>
                <div class="truncate">
                    <span class="font-extrabold text-xs sm:text-sm" style="color: var(--text-primary);">
                        Makan Kenyang, <span style="color: var(--brand);">Kantong Tenang!</span>
                    </span>
                    <span class="text-xs hidden lg:inline ml-2" style="color: var(--text-secondary);">
                        Pesan bebas antre dan nikmati hematnya jam istirahat.
                    </span>
                </div>
            </div>

            @php
                $isClaimed = false;
                $isUsed = false;
                if (auth()->check() && isset($voucher) && isset($userVoucherClaim) && $userVoucherClaim) {
                    $isClaimed = true;
                    $isUsed = (bool)$userVoucherClaim->is_used;
                } elseif (session()->has('active_voucher') && session('active_voucher.code') === 'KANTINHEMAT') {
                    $isClaimed = true;
                }
                $isExpired = isset($voucher) && $voucher ? $voucher->isExpired() : false;
                $discountPercent = isset($voucher) && $voucher ? $voucher->discount_percent : 20;
            @endphp

            {{-- Tombol Klik Interaktif Klaim Voucher Kupon (Border Putus-Putus / Dashed) --}}
            <div class="flex items-center gap-2 sm:gap-3 shrink-0 self-end md:self-auto"
                 x-data="voucherClaimWidget({
                    initialClaimed: {{ $isClaimed ? 'true' : 'false' }},
                    isUsed: {{ $isUsed ? 'true' : 'false' }},
                    isExpired: {{ $isExpired ? 'true' : 'false' }},
                    code: 'KANTINHEMAT',
                    discountPercent: {{ $discountPercent }}
                 })">
                <button
                    type="button"
                    @click="claimVoucher()"
                    :disabled="loading || isUsed"
                    id="btn-claim-voucher"
                    class="group inline-flex items-center gap-1.5 sm:gap-2 px-3 py-1.5 rounded-xl border-2 border-dashed transition-all duration-200 select-none shadow-xs"
                    :class="{
                        'border-amber-600/70 dark:border-amber-500/70 bg-amber-50/80 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 cursor-pointer': claimed && !isUsed,
                        'border-stone-300 dark:border-stone-700 bg-stone-100 dark:bg-stone-800/80 text-stone-400 dark:text-stone-500 cursor-not-allowed opacity-75': isUsed || isExpired,
                        'cursor-pointer hover:scale-[1.02] active:scale-95': !claimed && !isUsed && !isExpired
                    }"
                    :style="!claimed && !isUsed && !isExpired ? 'border-color: var(--brand-border); background-color: var(--bg-surface-2); color: var(--brand);' : ''"
                    :title="claimed ? 'Voucher diskon 20% aktif' : (isExpired ? 'Voucher telah kedaluwarsa' : 'Klik untuk klaim diskon 20%')"
                >
                    <span class="text-xs sm:text-sm" x-text="claimed ? '🎉' : (isExpired ? '⌛' : '🎟️')"></span>
                    <span class="font-mono font-extrabold tracking-wider text-xs sm:text-sm" x-text="code"></span>

                    {{-- Badge Status Diskon --}}
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] sm:text-[0.68rem] font-bold tracking-tight transition-colors shadow-2xs"
                        :class="{
                            'bg-amber-600 text-white': claimed && !isUsed,
                            'bg-stone-500 text-white': isUsed,
                            'bg-stone-400 text-white': isExpired,
                            'text-white': !claimed && !isUsed && !isExpired
                        }"
                        :style="!claimed && !isUsed && !isExpired ? 'background-color: var(--cta-accent);' : ''"
                    >
                        <template x-if="loading">
                            <span>Memproses...</span>
                        </template>
                        <template x-if="!loading && !claimed && !isUsed && !isExpired">
                            <span>Klaim 20%</span>
                        </template>
                        <template x-if="!loading && claimed && !isUsed">
                            <span>✓ Diskon 20% Aktif</span>
                        </template>
                        <template x-if="!loading && isUsed">
                            <span>Sudah Digunakan</span>
                        </template>
                        <template x-if="!loading && isExpired">
                            <span>Kedaluwarsa</span>
                        </template>
                    </span>
                </button>

                @if($heroFeatured)
                    <a href="#product-{{ $heroFeatured->id }}"
                       class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-lg transition-colors hover:underline"
                       style="color: var(--brand);">
                        <span>★ Menu Favorit</span>
                        <span>&darr;</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 3. INTEGRASI SEARCH BAR & FILTER KATEGORI TEPAT DI BAWAH HEADING ─ --}}
    <div x-data="catalogFilter()" class="mb-4">

        {{-- Baris Interaksi: Search Bar + Kategori Chips --}}
        <div class="flex flex-col md:flex-row gap-2.5 mb-3.5 items-stretch md:items-center justify-between">

            {{-- Kolom Pencarian Cepat (Search Bar Responsif) --}}
            <div class="relative flex-1 max-w-lg">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none" style="color: var(--text-muted);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Cari makanan, minuman, atau nama tenant..."
                    class="w-full pl-10 pr-9 py-2 text-sm rounded-xl border outline-none transition-colors shadow-xs"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                    id="menu-search"
                    aria-label="Cari menu makanan"
                >
                {{-- Tombol Hapus Pencarian --}}
                <button
                    x-show="search.length > 0"
                    @click="search = ''"
                    type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs opacity-60 hover:opacity-100 transition-opacity"
                    style="color: var(--text-muted);"
                    title="Hapus pencarian"
                >
                    ✕
                </button>
            </div>

            {{-- Filter Kategori (Semua, Makanan, Minuman, Camilan, Dessert) --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none shrink-0">
                <template x-for="cat in categories" :key="cat.value">
                    <button
                        @click="activeCategory = cat.value"
                        :class="activeCategory === cat.value ? 'category-tab-active shadow-xs' : 'category-tab-inactive'"
                        class="px-3.5 py-1.5 text-xs font-bold rounded-xl border transition-all duration-150 cursor-pointer whitespace-nowrap"
                        x-text="cat.label"
                        :aria-pressed="activeCategory === cat.value"
                    ></button>
                </template>
            </div>
        </div>

        {{-- Sub-header Rekomendasi Menu & Hitungan Menu (Terlihat Jelas Above The Fold) --}}
        <div class="flex items-center justify-between mb-3 pt-0.5">
            <div class="flex items-center gap-2">
                <h2 class="font-bold text-sm sm:text-base tracking-tight" style="color: var(--text-primary);">
                    Rekomendasi Menu Kantin
                </h2>
                <span class="text-[0.7rem] px-2 py-0.5 rounded-full font-semibold border"
                      style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--brand);"
                      x-text="totalFilteredText()">
                </span>
            </div>
            <span class="text-xs hidden sm:inline" style="color: var(--text-muted);">
                Pilihan hidangan siap saji tepat waktu
            </span>
        </div>

        {{-- ── GRID PRODUK ─────────────────────────────────────────────── --}}
        @if($products->isEmpty())
            {{-- State Kosong --}}
            <div class="text-center py-16">
                <p class="text-4xl mb-3">🍽️</p>
                <h2 class="text-lg font-semibold mb-1" style="color: var(--text-primary);">Belum ada menu tersedia</h2>
                <p class="text-sm" style="color: var(--text-muted);">Coba lagi nanti atau hubungi pengelola kantin.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3" id="product-grid">
                @foreach($products as $product)
                    <div
                        class="product-card"
                        x-show="matchesFilter('{{ strtolower($product->name) }} {{ strtolower($product->tenant->name) }}', '{{ $product->category }}')"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        id="product-{{ $product->id }}"
                    >
                        {{-- Gambar Produk (kecil, seragam) --}}
                        <div class="product-card__img"
                             style="background-color: var(--bg-surface-2);">
                            @if($product->image)
                                <img
                                    src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transition-transform duration-500 ease-out"
                                    loading="lazy"
                                >
                            @else
                                {{-- Placeholder SVG bila tidak ada gambar --}}
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-10 h-10 opacity-25" style="color: var(--text-muted);"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Subtle gradient overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-black/10 pointer-events-none"></div>

                            {{-- Badge Stok di atas gambar --}}
                            <div class="absolute top-2 left-2 z-10">
                                @if($product->stock <= 0 || !$product->is_available)
                                    <span class="badge-stock-out shadow-sm">Habis</span>
                                @elseif($product->stock <= 5)
                                    <span class="badge-stock-low shadow-sm">Sisa {{ $product->stock }}</span>
                                @else
                                    <span class="badge-stock-available shadow-sm">Tersedia</span>
                                @endif
                            </div>

                            {{-- Badge Unggulan --}}
                            @if($product->is_featured)
                                <div class="absolute top-2 right-2 z-10">
                                    <span class="text-[0.625rem] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shadow-xs flex items-center gap-1"
                                          style="background-color: var(--brand); color: var(--brand-text);">
                                        ★ Favorit
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Info Produk --}}
                        <div class="product-card__body">
                            {{-- Nama Tenant --}}
                            <a href="#"
                               class="text-[0.65rem] font-bold uppercase tracking-wider hover:opacity-80 transition-opacity block truncate"
                               style="color: var(--brand);">
                                {{ $product->tenant->name }}
                            </a>

                            {{-- Nama Produk --}}
                            <h3 class="font-bold text-sm mt-0.5 leading-snug truncate" style="color: var(--text-primary);" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h3>

                            {{-- Deskripsi singkat --}}
                            @if($product->description)
                                <p class="text-xs mt-1 line-clamp-2 leading-relaxed" style="color: var(--text-muted);">
                                    {{ $product->description }}
                                </p>
                            @else
                                <p class="text-xs mt-1 text-transparent select-none leading-relaxed">-</p>
                            @endif

                            {{-- Harga + Tombol (selalu di bawah) --}}
                            <div class="product-card__footer">
                                <span class="font-extrabold text-sm" style="color: var(--text-primary);">
                                    {{ $product->formatted_price }}
                                </span>

                                @if(!$isOpen)
                                    <span class="text-[0.65rem] sm:text-xs px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider"
                                          style="background-color: var(--status-danger-bg); color: var(--status-danger); border: 1px solid var(--status-danger-border);">
                                        Tutup
                                    </span>
                                @elseif($product->stock > 0 && $product->is_available)
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button
                                            type="button"
                                            onclick="handleAddToCart({
                                                productId: {{ $product->id }},
                                                name: {{ json_encode($product->name) }},
                                                price: {{ $product->price }},
                                                tenantId: {{ $product->tenant_id }},
                                                tenantName: {{ json_encode($product->tenant->name) }},
                                                image: {{ json_encode($product->image_url ?? '') }}
                                            }, this)"
                                            class="btn-cart text-xs shrink-0"
                                            id="add-to-cart-{{ $product->id }}"
                                            aria-label="Tambah {{ $product->name }} ke keranjang"
                                            title="Simpan ke keranjang belanja"
                                        >
                                            + Tambah
                                        </button>
                                        <button
                                            type="button"
                                            onclick="openDirectBuyModal({
                                                id: {{ $product->id }},
                                                name: {{ json_encode($product->name) }},
                                                price: {{ $product->price }},
                                                stock: {{ $product->stock }},
                                                tenantId: {{ $product->tenant_id }},
                                                tenantName: {{ json_encode($product->tenant->name) }},
                                                image: {{ json_encode($product->image_url ?? '') }}
                                            })"
                                            class="btn-buy-now text-xs shrink-0"
                                            id="buy-direct-{{ $product->id }}"
                                            aria-label="Beli langsung {{ $product->name }}"
                                            title="Beli langsung tanpa masuk keranjang"
                                        >
                                            ⚡ Beli
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs px-2.5 py-1 rounded-lg font-medium"
                                          style="background-color: var(--bg-surface-2); color: var(--text-muted); border: 1px solid var(--border-default);">
                                        Habis
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pesan "Tidak ada hasil filter" --}}
            <div
                x-show="filteredCount === 0"
                class="text-center py-12"
            >
                <p class="text-3xl mb-2">🔍</p>
                <p class="text-sm font-medium" style="color: var(--text-secondary);">Tidak ada menu yang cocok.</p>
                <button @click="search = ''; activeCategory = 'all'"
                        class="mt-3 text-xs hover:underline" style="color: var(--brand);">
                    Reset filter
                </button>
            </div>
        @endif

    </div>{{-- end x-data catalogFilter --}}

</div>
@endsection

@push('modals')
{{-- ══════════════════════════════════════════════════════════════════
     MODAL: BELI LANGSUNG (INSTANT FAST CHECKOUT POPUP)
══════════════════════════════════════════════════════════════════════ --}}
<div
    x-data="directBuyModal()"
    x-show="isOpen"
    x-cloak
    @keydown.escape.window="close()"
    class="relative z-[999]"
    role="dialog"
    aria-modal="true"
    aria-labelledby="direct-buy-title"
>
    {{-- 1. Backdrop Gelap dengan Blur --}}
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
        aria-hidden="true"
    ></div>

    {{-- 2. Container Panel Modal (Fixed di atas backdrop) --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
            <div
                x-show="isOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                @click.outside="close()"
                class="relative w-full max-w-lg transform overflow-hidden rounded-2xl border text-left shadow-2xl transition-all"
                style="background-color: var(--bg-surface); border-color: var(--border-default); color: var(--text-primary);"
            >
                {{-- Header Modal --}}
                <div class="px-5 py-4 border-b flex items-center justify-between" style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center text-base shadow-xs"
                              style="background: var(--brand-subtle); color: var(--brand); border: 1px solid var(--brand-border);">
                            ⚡
                        </span>
                        <div>
                            <h3 id="direct-buy-title" class="font-extrabold text-base tracking-tight" style="color: var(--text-primary);">
                                Beli Langsung
                            </h3>
                            <p class="text-xs" style="color: var(--text-muted);">
                                Pesan cepat tanpa antre, tanpa perlu masuk keranjang
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="close()"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-stone-500/20 text-stone-400 hover:text-stone-200 cursor-pointer"
                        aria-label="Tutup modal"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-5 max-h-[75vh] overflow-y-auto space-y-4">
                    {{-- Detail Produk Yang Dipilih --}}
                    <div class="flex items-center gap-3.5 p-3 rounded-xl border"
                         style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <template x-if="product?.image">
                            <img :src="product.image" :alt="product.name" class="w-16 h-16 rounded-xl object-cover shrink-0 border" style="border-color: var(--border-default);">
                        </template>
                        <template x-if="!product?.image">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center shrink-0 border text-2xl"
                                 style="background-color: var(--bg-surface-3); border-color: var(--border-default);">
                                🍱
                            </div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <span class="text-[0.7rem] font-bold uppercase tracking-wider block truncate"
                                  style="color: var(--brand);"
                                  x-text="product?.tenantName">
                            </span>
                            <h4 class="font-bold text-sm leading-snug truncate" style="color: var(--text-primary);" x-text="product?.name"></h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-extrabold text-sm" style="color: var(--brand);" x-text="'Rp ' + (product ? product.price.toLocaleString('id-ID') : '0')"></span>
                                <span class="text-[0.68rem] px-2 py-0.5 rounded-full font-medium"
                                      style="background-color: var(--bg-surface-3); color: var(--text-muted);"
                                      x-text="'Stok: ' + (product ? product.stock : 0)">
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Stepper Jumlah Porsi --}}
                    <div class="flex items-center justify-between p-3 rounded-xl border"
                         style="background-color: var(--bg-surface); border-color: var(--border-default);">
                        <div>
                            <span class="block text-xs font-bold" style="color: var(--text-primary);">Jumlah Porsi</span>
                            <span class="block text-[0.7rem]" style="color: var(--text-muted);">Maksimal sesuai stok tersedia</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="decreaseQty()"
                                :disabled="quantity <= 1"
                                class="w-8 h-8 rounded-lg border font-bold text-base flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:bg-stone-500/10 active:scale-95 cursor-pointer"
                                style="border-color: var(--border-default); color: var(--text-primary); background: var(--bg-surface-2);"
                                aria-label="Kurangi porsi"
                            >
                                −
                            </button>
                            <span class="w-8 text-center text-base font-extrabold" style="color: var(--text-primary);" x-text="quantity"></span>
                            <button
                                type="button"
                                @click="increaseQty()"
                                :disabled="product && quantity >= product.stock"
                                class="w-8 h-8 rounded-lg border font-bold text-base flex items-center justify-center transition-all disabled:opacity-30 disabled:cursor-not-allowed hover:bg-stone-500/10 active:scale-95 cursor-pointer"
                                style="border-color: var(--border-default); color: var(--text-primary); background: var(--bg-surface-2);"
                                aria-label="Tambah porsi"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Form Identitas Pembeli (Nama & Kelas) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="direct-buy-name" class="block text-xs font-semibold" style="color: var(--text-secondary);">
                                    Nama Pemesan <span style="color: var(--status-danger);">*</span>
                                </label>
                                <template x-if="isLoggedIn">
                                    <span class="text-[0.625rem] font-bold px-1.5 py-0.5 rounded-md"
                                          style="background-color: var(--brand-subtle); color: var(--brand);">
                                        🔒 Akun Login
                                    </span>
                                </template>
                            </div>
                            <input
                                type="text"
                                id="direct-buy-name"
                                name="customer_name"
                                x-model="customerName"
                                placeholder="Contoh: Budi Santoso"
                                required
                                :readonly="isLoggedIn"
                                :class="isLoggedIn ? 'cursor-not-allowed opacity-90' : ''"
                                class="w-full px-3 py-2 text-xs rounded-xl border outline-none transition-colors"
                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                            >
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="direct-buy-class" class="block text-xs font-semibold" style="color: var(--text-secondary);">
                                    Kelas <span style="color: var(--status-danger);">*</span>
                                </label>
                                <template x-if="isLoggedIn">
                                    <span class="text-[0.625rem] font-bold px-1.5 py-0.5 rounded-md"
                                          style="background-color: var(--brand-subtle); color: var(--brand);">
                                        🔒 Akun Login
                                    </span>
                                </template>
                            </div>
                            <input
                                type="text"
                                id="direct-buy-class"
                                name="customer_class"
                                x-model="customerClass"
                                placeholder="Contoh: 10 IPA 1"
                                required
                                :readonly="isLoggedIn"
                                :class="isLoggedIn ? 'cursor-not-allowed opacity-90' : ''"
                                class="w-full px-3 py-2 text-xs rounded-xl border outline-none transition-colors"
                                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                            >
                        </div>
                    </div>

                    {{-- Catatan Pesanan --}}
                    <div>
                        <label for="direct-buy-notes" class="block text-xs font-semibold mb-1" style="color: var(--text-secondary);">
                            Catatan Pesanan <span class="font-normal opacity-75">(opsional)</span>
                        </label>
                        <input
                            type="text"
                            id="direct-buy-notes"
                            x-model="notes"
                            placeholder="Contoh: pedas sedang, tanpa sayur, es terpisah, dll."
                            class="w-full px-3 py-2 text-xs rounded-xl border outline-none transition-colors"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                    </div>

                    {{-- Pilihan Metode Pembayaran --}}
                    <div>
                        <label class="block text-xs font-semibold mb-2" style="color: var(--text-secondary);">
                            Metode Pembayaran <span style="color: var(--status-danger);">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <label
                                class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all duration-150"
                                :style="paymentMethod === 'cash' ? 'border-color: var(--brand); background-color: var(--brand-subtle);' : 'border-color: var(--border-default); background-color: var(--bg-surface-2);'"
                            >
                                <input type="radio" x-model="paymentMethod" value="cash" class="w-3.5 h-3.5 cursor-pointer" style="accent-color: var(--brand);">
                                <div>
                                    <span class="block text-xs font-bold leading-tight" style="color: var(--text-primary);">💵 Tunai (Cash)</span>
                                    <span class="block text-[0.68rem]" style="color: var(--text-muted);">Bayar di kasir</span>
                                </div>
                            </label>
                            <label
                                class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition-all duration-150"
                                :style="paymentMethod === 'qris' ? 'border-color: var(--brand); background-color: var(--brand-subtle);' : 'border-color: var(--border-default); background-color: var(--bg-surface-2);'"
                            >
                                <input type="radio" x-model="paymentMethod" value="qris" class="w-3.5 h-3.5 cursor-pointer" style="accent-color: var(--brand);">
                                <div>
                                    <span class="block text-xs font-bold leading-tight" style="color: var(--text-primary);">📱 QRIS</span>
                                    <span class="block text-[0.68rem]" style="color: var(--text-muted);">Scan kode digital</span>
                                </div>
                            </label>
                        </div>

                        {{-- Panel Preview QRIS --}}
                        <div x-show="paymentMethod === 'qris'" x-transition class="mt-3">
                            <div class="p-3.5 rounded-xl border flex flex-col items-center justify-center text-center"
                                 style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                                <span class="font-bold text-xs mb-2" style="color: var(--brand);">Scan QRIS Kantin</span>
                                <div class="p-2 bg-white rounded-lg shadow-sm mb-2 border border-stone-200 mx-auto" style="width: 220px; max-width: 100%;">
                                    <img src="{{ asset('images/qris.jpg') }}?t={{ time() }}" alt="Barcode QRIS" class="w-full h-auto object-contain">
                                </div>
                                <p class="text-[0.68rem] leading-relaxed max-w-xs" style="color: var(--text-secondary);">
                                    Silakan scan dan bayar total <b style="color: var(--brand);" x-text="'Rp ' + total.toLocaleString('id-ID')"></b>, lalu klik tombol pesan di bawah.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Diskon Voucher Promo (Jika Ada Voucher Aktif) --}}
                    <template x-if="activeVoucher">
                        <div class="p-2.5 rounded-xl border border-dashed flex items-center justify-between text-xs"
                             style="background-color: var(--status-success-bg); border-color: var(--status-success-border); color: var(--status-success);">
                            <div class="flex items-center gap-1.5">
                                <span>🎟️</span>
                                <span>Voucher <b><span x-text="activeVoucher.code"></span></b> Aktif (<span x-text="activeVoucher.discount_percent"></span>% OFF)</span>
                            </div>
                            <span class="font-extrabold" x-text="'-Rp ' + discount.toLocaleString('id-ID')"></span>
                        </div>
                    </template>

                    {{-- Ringkasan Pembayaran & Total --}}
                    <div class="p-3.5 rounded-xl border space-y-1.5 text-xs"
                         style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <div class="flex justify-between" style="color: var(--text-secondary);">
                            <span>Subtotal (<span x-text="quantity"></span> porsi)</span>
                            <span x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
                        </div>
                        <template x-if="activeVoucher && discount > 0">
                            <div class="flex justify-between font-semibold" style="color: var(--status-success);">
                                <span>Potongan Voucher Promo</span>
                                <span x-text="'-Rp ' + discount.toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                        <div class="border-t pt-2 flex items-center justify-between font-extrabold text-sm"
                             style="border-color: var(--border-default);">
                            <span style="color: var(--text-primary);">Total Pembayaran</span>
                            <span style="color: var(--brand);" class="text-base" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    {{-- Alert Pesan Error --}}
                    <div x-show="errorMessage" x-transition class="p-2.5 rounded-xl text-xs font-medium border"
                         style="background-color: var(--status-danger-bg); border-color: var(--status-danger-border); color: var(--status-danger);"
                         x-text="errorMessage">
                    </div>
                </div>

                {{-- Footer Aksi Modal --}}
                <div class="px-5 py-3.5 border-t flex items-center justify-end gap-2.5"
                     style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                    <button
                        type="button"
                        @click="close()"
                        :disabled="loading"
                        class="px-4 py-2 text-xs font-semibold rounded-xl border transition-colors hover:opacity-80 cursor-pointer"
                        style="border-color: var(--border-default); color: var(--text-secondary); background: transparent;"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="submitOrder()"
                        :disabled="loading"
                        id="btn-confirm-direct-buy"
                        class="btn-brand text-xs px-5 py-2 rounded-xl font-extrabold shadow-md flex items-center gap-1.5 cursor-pointer"
                    >
                        <template x-if="loading">
                            <span>Memproses...</span>
                        </template>
                        <template x-if="!loading">
                            <span>⚡ Pesan Sekarang (<span x-text="'Rp ' + total.toLocaleString('id-ID')"></span>)</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    // ─── Alpine Component: Catalog Filter ────────────────────────────────────
    function catalogFilter() {
        return {
            search: '',
            activeCategory: 'all',
            filteredCount: {{ $products->count() }},

            categories: [
                { value: 'all',           label: 'Semua' },
                { value: 'makanan_berat', label: 'Makanan' },
                { value: 'minuman',       label: 'Minuman' },
                { value: 'makanan_ringan',label: 'Camilan' },
                { value: 'dessert',       label: 'Dessert' },
            ],

            matchesFilter(searchStr, category) {
                const q = this.search.trim().toLowerCase();
                const searchMatch = !q || searchStr.toLowerCase().includes(q);
                const catMatch = this.activeCategory === 'all' || category === this.activeCategory;
                return searchMatch && catMatch;
            },

            totalFilteredText() {
                if (this.search.trim().length > 0) {
                    return this.filteredCount + ' hasil';
                }
                return this.filteredCount + ' menu';
            },

            init() {
                this.$watch('search', () => this.updateCount());
                this.$watch('activeCategory', () => this.updateCount());
                this.updateCount();
            },

            updateCount() {
                this.$nextTick(() => {
                    const cards = this.$el.querySelectorAll('#product-grid > .product-card');
                    let visible = 0;
                    cards.forEach(card => {
                        if (card.style.display !== 'none') {
                            visible++;
                        }
                    });
                    this.filteredCount = visible;
                });
            },
        };
    }

    // ─── Add to Cart Handler ─────────────────────────────────────────────────
    function handleAddToCart(item, btn) {
        const result = window.addToCart(item);

        if (!result.success) {
            // Konfirmasi dari user untuk kosongkan cart
            if (confirm(result.message + '\n\nKosongkan keranjang dan pesan dari ' + item.tenantName + '?')) {
                sessionStorage.removeItem('ekantin_cart');
                window.addToCart(item);
                window.refreshCartBadge();
                showAddedFeedback(btn);
            }
            return;
        }

        window.refreshCartBadge();
        showAddedFeedback(btn);
    }

    // Feedback visual saat item ditambah
    function showAddedFeedback(btn) {
        const original = btn.textContent;
        btn.textContent = '✓ Ditambah';
        btn.disabled = true;
        btn.style.opacity = '0.7';

        setTimeout(() => {
            btn.textContent = original;
            btn.disabled = false;
            btn.style.opacity = '';
        }, 1500);
    }

    // ─── Alpine Component: Voucher Claim Widget ──────────────────────────────
    function voucherClaimWidget(config) {
        return {
            claimed: config.initialClaimed || false,
            isUsed: config.isUsed || false,
            isExpired: config.isExpired || false,
            code: config.code || 'KANTINHEMAT',
            discountPercent: config.discountPercent || 20,
            loading: false,

            async claimVoucher() {
                if (this.loading || this.isUsed) return;

                // 1. Cek masa kedaluwarsa di sisi client
                if (this.isExpired) {
                    Swal.fire({
                        title: 'Voucher Kedaluwarsa ⌛',
                        text: 'Maaf, masa aktif voucher promo ' + this.code + ' telah berakhir.',
                        icon: 'error',
                        confirmButtonColor: '#D97706',
                        confirmButtonText: 'Tutup'
                    });
                    return;
                }

                // 2. Jika sudah pernah diklaim sebelumnya dan belum digunakan
                if (this.claimed) {
                    Swal.fire({
                        title: 'Diskon 20% Sudah Aktif! 🎉',
                        text: 'Voucher ' + this.code + ' sudah tersimpan untuk akun Anda. Potongan 20% otomatis diaplikasikan di keranjang belanja!',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#D97706',
                        cancelButtonColor: '#78716C',
                        confirmButtonText: 'Buka Keranjang',
                        cancelButtonText: 'Lanjut Pilih Menu'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("cart.index") }}';
                        }
                    });
                    return;
                }

                // 3. Jalankan aksi klaim langsung via AJAX / Fetch API tanpa reload halaman
                this.loading = true;

                try {
                    const response = await fetch('{{ route("voucher.claim") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ code: this.code })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        this.claimed = true;
                        if (data.voucher) {
                            sessionStorage.setItem('ekantin_active_voucher', JSON.stringify(data.voucher));
                        }

                        // Notifikasi diskon 20% langsung muncul aktif di layar tanpa reload halaman
                        Swal.fire({
                            title: 'Diskon 20% Aktif! 🎟️',
                            html: `
                                <div class="text-sm mt-1 text-stone-600 dark:text-stone-300">
                                    <p class="font-medium mb-3">${data.message}</p>
                                    <div class="p-3 bg-amber-50 dark:bg-stone-800 rounded-xl border border-amber-200/80 dark:border-stone-700 text-amber-900 dark:text-amber-200 text-xs text-left">
                                        <div class="flex items-center gap-2 font-bold mb-1">
                                            <span>✨</span>
                                            <span>Kupon: ${data.voucher.code} (Diskon ${data.voucher.discount_percent}%)</span>
                                        </div>
                                        <p class="opacity-80">Potongan harga 20% langsung aktif dan otomatis memotong total belanja di keranjang Anda!</p>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#D97706',
                            cancelButtonColor: '#78716C',
                            confirmButtonText: 'Lihat Keranjang',
                            cancelButtonText: 'Lanjut Belanja'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("cart.index") }}';
                            }
                        });
                    } else if (data.require_login) {
                        Swal.fire({
                            title: 'Perlu Masuk Akun 🔐',
                            text: data.message || 'Silakan masuk ke akun Anda terlebih dahulu untuk mengklaim voucher ini (1 akun hanya bisa klaim 1 kali).',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#D97706',
                            cancelButtonColor: '#78716C',
                            confirmButtonText: 'Masuk Sekarang',
                            cancelButtonText: 'Nanti Saja'
                        }).then((res) => {
                            if (res.isConfirmed) {
                                window.location.href = data.login_url || '{{ route("login") }}';
                            }
                        });
                    } else {
                        // Aturan Bisnis & Validasi Backend: Kedaluwarsa atau Satu Akun Satu Kali
                        if (data.expired) {
                            this.isExpired = true;
                        }
                        if (data.already_claimed) {
                            this.claimed = true;
                            if (data.is_used) {
                                this.isUsed = true;
                            }
                        }

                        Swal.fire({
                            title: data.expired ? 'Voucher Kedaluwarsa ⌛' : (data.already_claimed ? 'Klaim Ditolak' : 'Gagal Klaim Voucher'),
                            text: data.message || 'Tidak dapat mengklaim voucher saat ini.',
                            icon: data.already_claimed ? 'warning' : 'error',
                            confirmButtonColor: '#D97706',
                            confirmButtonText: 'Mengerti'
                        });
                    }
                } catch (error) {
                    console.error('Gagal klaim voucher:', error);
                    Swal.fire({
                        title: 'Koneksi Terputus',
                        text: 'Terjadi gangguan saat memproses voucher. Silakan periksa jaringan Anda.',
                        icon: 'error',
                        confirmButtonColor: '#D97706',
                        confirmButtonText: 'OK'
                    });
                } finally {
                    this.loading = false;
                }
            }
        };
    }

    // ─── Alpine Component: Beli Langsung (Direct Instant Buy Modal) ──────────
    function directBuyModal() {
        return {
            isOpen: false,
            product: null,
            quantity: 1,
            isLoggedIn: @json(auth()->check()),
            customerName: @json(auth()->check() ? auth()->user()->name : ''),
            customerClass: @json(auth()->check() ? (auth()->user()->display_classroom ?? auth()->user()->classroom ?? '') : ''),
            notes: '',
            paymentMethod: 'cash',
            activeVoucher: null,
            loading: false,
            errorMessage: '',

            init() {
                // Jika belum login, gunakan data tersimpan di browser jika ada
                if (!this.isLoggedIn) {
                    const savedName = localStorage.getItem('ekantin_customer_name') || '';
                    const savedClass = localStorage.getItem('ekantin_customer_class') || '';
                    if (savedName) this.customerName = savedName;
                    if (savedClass) this.customerClass = savedClass;
                }

                this.checkVoucher();

                // Listen trigger event buka modal dari tombol kartu menu
                window.addEventListener('open-direct-buy', (e) => {
                    this.open(e.detail);
                });
            },

            checkVoucher() {
                const saved = sessionStorage.getItem('ekantin_active_voucher');
                if (saved) {
                    try {
                        this.activeVoucher = JSON.parse(saved);
                    } catch(e) {
                        this.activeVoucher = null;
                    }
                } else {
                    this.activeVoucher = null;
                }
            },

            open(productData) {
                this.product = productData;
                this.quantity = 1;
                this.notes = '';
                this.paymentMethod = 'cash';
                this.errorMessage = '';
                this.checkVoucher();

                if (!this.customerName) {
                    this.customerName = localStorage.getItem('ekantin_customer_name') || '{{ auth()->check() ? addslashes(auth()->user()->name) : '' }}';
                }
                if (!this.customerClass) {
                    this.customerClass = localStorage.getItem('ekantin_customer_class') || '';
                }

                this.isOpen = true;
                document.body.style.overflow = 'hidden';
            },

            close() {
                this.isOpen = false;
                document.body.style.overflow = '';
            },

            increaseQty() {
                if (this.product && this.quantity < this.product.stock) {
                    this.quantity++;
                }
            },

            decreaseQty() {
                if (this.quantity > 1) {
                    this.quantity--;
                }
            },

            get subtotal() {
                if (!this.product) return 0;
                return this.product.price * this.quantity;
            },

            get discount() {
                if (!this.activeVoucher || !this.activeVoucher.discount_percent) return 0;
                return Math.round((this.subtotal * this.activeVoucher.discount_percent) / 100);
            },

            get total() {
                return Math.max(0, this.subtotal - this.discount);
            },

            async submitOrder() {
                if (!this.product) return;

                const name = this.customerName.trim();
                const cls = this.customerClass.trim();

                if (!name || !cls) {
                    this.errorMessage = 'Mohon isi Nama Pemesan dan Kelas terlebih dahulu.';
                    return;
                }

                this.errorMessage = '';
                this.loading = true;

                // Simpan agar pemesanan berikutnya otomatis terisi
                localStorage.setItem('ekantin_customer_name', name);
                localStorage.setItem('ekantin_customer_class', cls);

                try {
                    const response = await fetch('{{ route("checkout.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            items: [{
                                id: this.product.id,
                                quantity: this.quantity
                            }],
                            tenant_id: this.product.tenantId,
                            customer_name: name,
                            customer_class: cls,
                            payment_method: this.paymentMethod,
                            notes: this.notes.trim() || null,
                            voucher_code: this.activeVoucher ? this.activeVoucher.code : null
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        if (this.activeVoucher) {
                            sessionStorage.removeItem('ekantin_active_voucher');
                        }
                        this.close();

                        Swal.fire({
                            title: 'Pesanan Diterima! ⚡',
                            text: 'Pesanan langsung diteruskan ke ' + this.product.tenantName,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else {
                        this.errorMessage = data.message || 'Gagal memproses pesanan.';
                        Swal.fire({
                            title: 'Pesanan Gagal',
                            text: data.message || 'Terjadi kesalahan saat memproses pesanan.',
                            icon: 'error',
                            confirmButtonColor: '#E05326'
                        });
                    }
                } catch (err) {
                    console.error('Direct buy error:', err);
                    this.errorMessage = 'Terjadi gangguan jaringan. Silakan coba lagi.';
                    Swal.fire({
                        title: 'Gangguan Koneksi',
                        text: 'Tidak dapat menghubungi server. Periksa jaringan Anda.',
                        icon: 'error',
                        confirmButtonColor: '#E05326'
                    });
                } finally {
                    this.loading = false;
                }
            }
        };
    }

    // Helper pemicu modal beli langsung dari tombol kartu
    function openDirectBuyModal(productData) {
        window.dispatchEvent(new CustomEvent('open-direct-buy', { detail: productData }));
    }
</script>
@endpush
