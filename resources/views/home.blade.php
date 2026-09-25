@extends('layouts.app')

@section('title', 'Menu Kantin')
@section('meta_description', 'Jelajahi menu makanan dan minuman dari berbagai tenant kantin. Pesan sekarang tanpa antre!')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- ── HERO ────────────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mb-1.5" style="color: var(--text-primary);">
            Mau pesan apa hari ini?
        </h1>
        <p class="text-sm sm:text-base" style="color: var(--text-secondary);">
            Pilih menu favoritmu dari kantin pilihan — tanpa harus mengantri.
        </p>
    </div>

    {{-- ── FILTER & PENCARIAN ──────────────────────────────────────────── --}}
    <div x-data="catalogFilter()" class="mb-6">

        {{-- Baris filter: Search + Kategori --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-4">

            {{-- Search Box --}}
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                     style="color: var(--text-muted);"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Cari menu..."
                    class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border outline-none transition-colors duration-150 focus:ring-2"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                    id="menu-search"
                    aria-label="Cari menu makanan"
                >
            </div>

            {{-- Filter Kategori --}}
            <div class="flex items-center gap-2 flex-wrap">
                <template x-for="cat in categories" :key="cat.value">
                    <button
                        @click="activeCategory = cat.value"
                        :class="activeCategory === cat.value ? 'btn-brand' : ''"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-all duration-150"
                        :style="activeCategory !== cat.value ? 'background-color: var(--bg-surface); color: var(--text-secondary); border-color: var(--border-default);' : 'border-color: transparent;'"
                        x-text="cat.label"
                        :aria-pressed="activeCategory === cat.value"
                    ></button>
                </template>
            </div>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="product-grid">
                @foreach($products as $product)
                    <div
                        class="product-card"
                        x-show="matchesFilter('{{ strtolower($product->name) }} {{ strtolower($product->tenant->name) }}', '{{ $product->category }}')"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        id="product-{{ $product->id }}"
                    >
                        {{-- Gambar Produk --}}
                        <div class="relative h-40 overflow-hidden"
                             style="background-color: var(--bg-surface-2);">
                            @if($product->image)
                                <img
                                    src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            @else
                                {{-- Placeholder SVG bila tidak ada gambar --}}
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 opacity-25" style="color: var(--text-muted);"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Badge Stok di atas gambar --}}
                            <div class="absolute top-2.5 left-2.5">
                                @if($product->stock <= 0 || !$product->is_available)
                                    <span class="badge-stock-out">Habis</span>
                                @elseif($product->stock <= 5)
                                    <span class="badge-stock-low">Sisa {{ $product->stock }}</span>
                                @else
                                    <span class="badge-stock-available">Tersedia</span>
                                @endif
                            </div>

                            {{-- Badge Unggulan --}}
                            @if($product->is_featured)
                                <div class="absolute top-2.5 right-2.5">
                                    <span class="text-[0.625rem] font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider"
                                          style="background-color: var(--brand); color: #fff;">
                                        Unggulan
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Info Produk --}}
                        <div class="p-4">
                            {{-- Nama Tenant --}}
                            <a href="#"
                               class="text-[0.65rem] font-semibold uppercase tracking-wider hover:opacity-75 transition-opacity"
                               style="color: var(--brand);">
                                {{ $product->tenant->name }}
                            </a>

                            {{-- Nama Produk --}}
                            <h3 class="font-semibold text-sm mt-0.5 leading-snug" style="color: var(--text-primary);">
                                {{ $product->name }}
                            </h3>

                            {{-- Deskripsi singkat --}}
                            @if($product->description)
                                <p class="text-xs mt-1 line-clamp-2 leading-relaxed" style="color: var(--text-muted);">
                                    {{ $product->description }}
                                </p>
                            @endif

                            {{-- Harga + Tombol --}}
                            <div class="flex items-center justify-between mt-3.5 gap-2">
                                <span class="font-bold text-sm" style="color: var(--text-primary);">
                                    {{ $product->formatted_price }}
                                </span>

                                @if($product->stock > 0 && $product->is_available)
                                    <button
                                        onclick="handleAddToCart({
                                            productId: {{ $product->id }},
                                            name: '{{ addslashes($product->name) }}',
                                            price: {{ $product->price }},
                                            tenantId: {{ $product->tenant_id }},
                                            tenantName: '{{ addslashes($product->tenant->name) }}'
                                        }, this)"
                                        class="btn-inverse text-xs shrink-0"
                                        id="add-to-cart-{{ $product->id }}"
                                        aria-label="Tambah {{ $product->name }} ke keranjang"
                                    >
                                        + Tambah
                                    </button>
                                @else
                                    <span class="text-xs px-3 py-1.5 rounded-lg font-medium"
                                          style="background-color: var(--bg-surface-2); color: var(--text-muted);">
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

@push('scripts')
<script>
    // ─── Alpine Component: Catalog Filter ────────────────────────────────────
    function catalogFilter() {
        return {
            search: '',
            activeCategory: 'all',
            filteredCount: 0,

            categories: [
                { value: 'all',           label: 'Semua' },
                { value: 'makanan_berat', label: 'Makanan Berat' },
                { value: 'makanan_ringan',label: 'Camilan' },
                { value: 'minuman',       label: 'Minuman' },
                { value: 'dessert',       label: 'Dessert' },
            ],

            matchesFilter(searchStr, category) {
                const searchMatch = !this.search
                    || searchStr.toLowerCase().includes(this.search.toLowerCase());
                const catMatch = this.activeCategory === 'all'
                    || category === this.activeCategory;
                return searchMatch && catMatch;
            },

            init() {
                this.$watch('search', () => this.updateCount());
                this.$watch('activeCategory', () => this.updateCount());
            },

            updateCount() {
                this.$nextTick(() => {
                    this.filteredCount = this.$el
                        .querySelectorAll('#product-grid > div[style*="display: block"], #product-grid > div:not([style*="display: none"])')
                        .length;
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
</script>
@endpush
