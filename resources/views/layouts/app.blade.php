<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── SEO ─────────────────────────────────────────────────────────── --}}
    <title>@yield('title', 'KantinSchool') — Pesan Makanan Tanpa Antre</title>
    <meta name="description" content="@yield('meta_description', 'Pesan makanan dari kantin favoritmu secara digital, mudah, cepat, tanpa harus mengantri.')">

    {{-- ── Favicon (SVG inline, tidak perlu file eksternal) ───────────── --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍱</text></svg>">

    {{-- ── Google Fonts: Plus Jakarta Sans & Inter ───────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- ── Vite: Tailwind CSS + App JS ───────────────────────────────── --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ── Alpine.js (CDN — di head agar x-cloak bekerja) ────────────── --}}
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ══════════════════════════════════════════════════════════════════
         ANTI-FLASH DARK MODE SCRIPT
         Harus berjalan SEBELUM browser me-render body apa pun.
         Script ini membaca localStorage dan menerapkan class .dark ke <html>
         secara sinkron sehingga tidak ada kedipan (FOUC).
         Fallback: jika tidak ada preferensi tersimpan, ikuti preferensi OS.
    ══════════════════════════════════════════════════════════════════════ --}}
    <script>
        (function () {
            var root = document.documentElement;
            // Migrasi otomatis ke tema Kayu Alami (Wood Texture & Organic)
            var themeVersion = localStorage.getItem('ekantin_palette_ver');
            if (themeVersion !== 'wood_organic_v1') {
                localStorage.setItem('ekantin_palette_ver', 'wood_organic_v1');
                localStorage.setItem('ekantin_theme', 'light');
            }
            var stored = localStorage.getItem('ekantin_theme');

            function applyTheme(isDark) {
                if (isDark) {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
            }

            if (stored === 'dark') {
                applyTheme(true);
            } else {
                applyTheme(false);
            }

            // Hapus class no-transition setelah DOM siap agar animasi berjalan normal
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    root.classList.remove('no-transition');
                }, 50);
            });
        })();
    </script>

    @stack('head')
</head>

{{-- ══════════════════════════════════════════════════════════════════════════
     BODY — Alpine.js x-data untuk state dark mode di komponen seluruh halaman
════════════════════════════════════════════════════════════════════════════ --}}
<body
    x-data="{
        darkMode: document.documentElement.classList.contains('dark'),
        cartCount: 0,

        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('ekantin_theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('ekantin_theme', 'light');
            }
        },

        init() {
            this.cartCount = window.getCartCount ? window.getCartCount() : 0;
        }
    }"
    @cart-updated.window="cartCount = $event.detail.count"
    class="min-h-screen font-sans antialiased"
    style="color: var(--text-primary);"
>

    {{-- ══════════════════════════════════════════════════════════════════
         BACKGROUND ATMOSPHERE & AMBIENT GLOW EFFECTS (Halus & Tenang)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-ambient-container" aria-hidden="true">
        @include('partials.meteor-shower')
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════════════════════════════════════ --}}
    <header class="navbar relative z-20" id="main-navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

            {{-- Brand Logo Organik Kayu --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group" aria-label="KantinSchool Beranda">
                <span class="w-8 h-8 rounded-xl flex items-center justify-center text-base shadow-2xs border transition-transform duration-200 group-hover:scale-105"
                      style="background-color: var(--brand-subtle); border-color: var(--brand-border);">
                    🌿
                </span>
                <div class="flex flex-col">
                    <span class="font-extrabold text-base tracking-tight leading-none" style="color: var(--brand);">KantinSchool</span>
                    <span class="text-[0.625rem] font-semibold tracking-wider uppercase mt-0.5" style="color: var(--text-muted);">Pesan Cepat Tanpa Antre</span>
                </div>
            </a>


            {{-- Aksi Kanan --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- Toggle Dark/Light Mode --}}
                <button
                    @click="toggleTheme()"
                    class="theme-toggle"
                    :aria-label="darkMode ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
                    :title="darkMode ? 'Mode Terang' : 'Mode Gelap'"
                    id="theme-toggle-btn"
                >
                    {{-- Ikon Matahari: tampil saat dark mode aktif --}}
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{-- Ikon Bulan: tampil saat light mode aktif --}}
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>

                {{-- Pesanan Saya --}}
                <a href="{{ route('orders.index') }}"
                   class="relative flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150"
                   style="color: var(--text-primary); {{ request()->routeIs('orders.index') ? 'background-color: var(--bg-surface-2);' : 'hover:background-color: var(--bg-surface-2);' }}"
                   aria-label="Pesanan Saya"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="hidden sm:inline">Pesanan Saya</span>
                </a>

                {{-- Keranjang Belanja --}}
                <a href="{{ route('cart.index') }}"
                   class="relative flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150"
                   style="background-color: var(--bg-surface-2); color: var(--text-primary);"
                   id="cart-link"
                   aria-label="Keranjang belanja"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.35 2.7A1 1 0 007 17h10M7 13L5.4 5M17 17a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span class="hidden sm:inline">Keranjang</span>
                    {{-- Badge jumlah item (10% CTA Accent) --}}
                    <span
                        x-show="cartCount > 0"
                        x-text="cartCount > 99 ? '99+' : cartCount"
                        class="absolute -top-1.5 -right-1.5 min-w-[1.125rem] h-[1.125rem] px-1 text-[0.625rem] font-bold leading-none rounded-full flex items-center justify-center shadow-md animate-pulse"
                        style="background-color: var(--cta-accent); color: #ffffff; box-shadow: 0 0 8px var(--cta-accent-glow);"
                    ></span>
                </a>

                {{-- Tombol Langsung ke Dashboard Pengelola (Khusus Admin / Tenant) --}}
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isTenant())
                        <a href="{{ route('dashboard.index') }}"
                           class="relative flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg border transition-all duration-150 hover:scale-[1.02]"
                           style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);"
                           title="Buka Panel Dashboard Admin Kantin"
                        >
                            <span class="text-xs">📊</span>
                            <span class="hidden md:inline">Dashboard</span>
                        </a>
                    @endif
                @endauth

                {{-- Lonceng Notifikasi Pesanan (Notification Bell + Popover Dropdown) --}}
                @php
                    $navUserOrders = auth()->check()
                        ? auth()->user()->orders()->with(['items', 'tenant'])->latest()->take(6)->get()
                        : collect();
                    $unreadNotifCount = $navUserOrders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])->count();
                @endphp
                    <div class="relative" x-data="{
                        openNotif: false,
                        hasUnread: {{ $unreadNotifCount > 0 ? 'true' : 'false' }},
                        markAsRead() {
                            this.hasUnread = false;
                        }
                    }">
                        <button
                            @click="openNotif = !openNotif; if (openNotif) markAsRead()"
                            @click.outside="openNotif = false"
                            type="button"
                            class="relative flex items-center justify-center w-9 h-9 rounded-xl border transition-all duration-150 hover:scale-105 active:scale-95 cursor-pointer"
                            style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-primary);"
                            id="btn-notification-bell"
                            aria-label="Notifikasi status pesanan"
                            title="Notifikasi Status Pesanan"
                        >
                            {{-- Ikon Lonceng SVG --}}
                            <svg class="w-4 h-4 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            {{-- Indikator Titik Merah (Pulse Animation) Jika Ada Notifikasi Baru --}}
                            <span
                                x-show="hasUnread"
                                class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5"
                                title="Ada notifikasi pesanan aktif"
                            >
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500 border border-white dark:border-stone-900"></span>
                            </span>
                        </button>

                        {{-- Popover Dropdown Notifikasi --}}
                        <div
                            x-show="openNotif"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl shadow-xl z-50 overflow-hidden border backdrop-blur-md"
                            style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card-hover);"
                            id="notification-popover"
                        >
                            {{-- Header Popover --}}
                            <div class="px-4 py-3 border-b flex items-center justify-between"
                                 style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">🔔</span>
                                    <h3 class="text-xs font-bold" style="color: var(--text-primary);">Status Pesanan Terbaru</h3>
                                </div>
                                <template x-if="hasUnread">
                                    <span class="text-[0.625rem] font-bold px-2 py-0.5 rounded-full"
                                          style="background-color: var(--cta-accent); color: #ffffff;">
                                        Baru
                                    </span>
                                </template>
                            </div>

                            {{-- Daftar Status Pesanan Terbaru --}}
                            <div class="max-h-80 overflow-y-auto divide-y" style="border-color: var(--border-default);">
                                @forelse($navUserOrders as $orderNotif)
                                    @php
                                        $mainItem = $orderNotif->items->first();
                                        $dishName = $mainItem ? $mainItem->product_name : 'Pesanan';
                                        $otherCount = $orderNotif->items->count() - 1;
                                        if ($otherCount > 0) {
                                            $dishName .= ' (+' . $otherCount . ' item)';
                                        }
                                    @endphp
                                    <a href="{{ route('orders.index') }}"
                                       class="block px-4 py-3 transition-colors duration-150 hover:bg-stone-500/5 group">
                                        <div class="flex items-start gap-3">
                                            {{-- Icon Status --}}
                                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm mt-0.5 shadow-2xs"
                                                 @if($orderNotif->status === 'ready')
                                                     style="background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;"
                                                 @elseif($orderNotif->status === 'preparing')
                                                     style="background-color: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;"
                                                 @elseif($orderNotif->status === 'confirmed')
                                                     style="background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;"
                                                 @elseif($orderNotif->status === 'completed')
                                                     style="background-color: #f5f5f4; color: #57534e; border: 1px solid #e7e5e4;"
                                                 @elseif($orderNotif->status === 'cancelled')
                                                     style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;"
                                                 @else
                                                     style="background-color: var(--bg-surface-2); color: var(--text-muted); border: 1px solid var(--border-default);"
                                                 @endif
                                            >
                                                @if($orderNotif->status === 'ready')
                                                    🟢
                                                @elseif($orderNotif->status === 'preparing')
                                                    🍳
                                                @elseif($orderNotif->status === 'confirmed')
                                                    ⏳
                                                @elseif($orderNotif->status === 'completed')
                                                    ✓
                                                @elseif($orderNotif->status === 'cancelled')
                                                    ✕
                                                @else
                                                    📄
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold leading-snug group-hover:underline" style="color: var(--text-primary);">
                                                    @if($orderNotif->status === 'ready')
                                                        Pesanan <b>{{ $dishName }}</b> siap diambil di kantin!
                                                    @elseif($orderNotif->status === 'preparing')
                                                        Pesanan <b>{{ $dishName }}</b> Anda sedang disiapkan oleh {{ $orderNotif->tenant->name ?? 'Kantin' }}.
                                                    @elseif($orderNotif->status === 'confirmed')
                                                        Pesanan <b>{{ $dishName }}</b> telah dikonfirmasi penjual.
                                                    @elseif($orderNotif->status === 'completed')
                                                        Pesanan <b>{{ $dishName }}</b> selesai diambil. Selamat menikmati!
                                                    @elseif($orderNotif->status === 'cancelled')
                                                        Pesanan <b>{{ $dishName }}</b> dibatalkan.
                                                    @else
                                                        Pesanan <b>{{ $dishName }}</b> sedang menunggu konfirmasi kasir.
                                                    @endif
                                                </p>
                                                <div class="flex items-center gap-2 mt-1 text-[0.68rem]" style="color: var(--text-muted);">
                                                    <span class="font-mono font-bold">{{ $orderNotif->order_number }}</span>
                                                    <span>•</span>
                                                    <span>{{ $orderNotif->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-8 px-4 text-center">
                                        <span class="text-2xl block mb-1">📭</span>
                                        <p class="text-xs font-semibold" style="color: var(--text-primary);">Belum Ada Status Pesanan</p>
                                        <p class="text-[0.7rem] mt-0.5 mb-2" style="color: var(--text-muted);">Pesanan yang Anda buat akan langsung terpantau di sini.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Footer Popover --}}
                            <div class="p-2.5 border-t text-center flex items-center justify-between px-4 text-xs"
                                 style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
                                <a href="{{ route('orders.index') }}"
                                   class="font-bold inline-flex items-center gap-1 hover:underline transition-colors"
                                   style="color: var(--brand);">
                                    <span>Lihat Semua Pesanan Saya</span>
                                    <span>&rarr;</span>
                                </a>
                                <button
                                    @click="markAsRead(); openNotif = false"
                                    type="button"
                                    class="text-[0.68rem] font-medium opacity-70 hover:opacity-100 transition-opacity"
                                    style="color: var(--text-secondary);"
                                >
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Profil Pengguna (Jika Login) / Tombol Masuk (Jika Tamu) --}}
                    @auth
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            class="flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg transition-colors duration-150 border"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                        >
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                                  style="background-color: var(--brand); color: var(--brand-text);">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden sm:inline max-w-[8rem] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="{'rotate-180': open}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 mt-2 w-52 rounded-xl py-1 z-50 origin-top-right shadow-lg backdrop-blur-md"
                            style="background-color: var(--bg-surface); border: 1px solid var(--border-default); box-shadow: var(--shadow-card-hover);"
                        >
                            <div class="px-4 py-2.5 border-b" style="border-color: var(--border-default);">
                                <p class="text-xs" style="color: var(--text-muted);">Masuk sebagai</p>
                                <p class="text-sm font-medium truncate" style="color: var(--text-primary);">{{ auth()->user()->email }}</p>
                            </div>
                            @if(auth()->user()->isAdmin() || auth()->user()->isTenant())
                            <a href="{{ route('dashboard.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm font-semibold transition-colors duration-100 hover:opacity-85"
                               style="color: var(--brand);">
                                <span>📊</span>
                                <span>Dashboard Admin</span>
                            </a>
                            @endif
                            <a href="{{ route('dashboard.stock.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors duration-100 hover:opacity-80"
                               style="color: var(--text-secondary);">
                                <span>🌾</span>
                                <span>Stok Bahan & Produk</span>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors duration-100 hover:opacity-80"
                               style="color: var(--text-secondary);">
                                <span>👤</span>
                                <span>Profil Akun</span>
                            </a>
                            <div class="border-t my-1" style="border-color: var(--border-default);"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-sm transition-colors duration-100 hover:opacity-80"
                                        style="color: var(--status-danger);">
                                    <span>🚪</span>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}"
                       class="btn-brand text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs inline-flex items-center gap-1.5"
                       title="Masuk ke Akun KantinSchool"
                       id="nav-login-btn"
                    >
                        <span>Masuk</span>
                    </a>
                    @endauth
                </div>
            </div>
    </header>

    {{-- ══════════════════════════════════════════════════════════════════
         FLASH MESSAGES (Warna Muted & Elegan)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if (session('success') || session('error') || session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4" role="alert">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm border backdrop-blur-md"
                     style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm border backdrop-blur-md"
                     style="background-color: var(--status-danger-bg); color: var(--status-danger); border-color: var(--status-danger-border);">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
            @if (session('info'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm border backdrop-blur-md"
                     style="background-color: var(--status-info-bg); color: var(--status-info); border-color: var(--status-info-border);">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>{{ session('info') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         KONTEN HALAMAN
    ══════════════════════════════════════════════════════════════════════ --}}
    <main id="main-content" class="relative z-10" tabindex="-1">
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════════════════════
         FOOTER MINIMALIS & RESPONSIF (KantinSchool)
    ══════════════════════════════════════════════════════════════════════ --}}
    <footer class="mt-12 border-t relative z-10 backdrop-blur-md"
            style="border-color: var(--border-default); background-color: color-mix(in srgb, var(--bg-surface) 80%, transparent);"
            x-data="footerSupportModal()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-xs" style="color: var(--text-muted);">
                
                {{-- Kiri: Brand & Hak Cipta --}}
                <div class="flex items-center gap-2 text-center md:text-left">
                    <span class="text-base select-none" aria-hidden="true">🌿🍱</span>
                    <span class="font-extrabold tracking-tight" style="color: var(--brand);">KantinSchool</span>
                    <span class="hidden sm:inline" style="color: var(--border-strong);">•</span>
                    <p class="text-[0.72rem] sm:text-xs">
                        &copy; {{ date('Y') }} KantinSchool. Hak cipta dilindungi.
                    </p>
                </div>

                {{-- Kanan: Tautan Berguna Minimalis --}}
                <div class="flex items-center flex-wrap justify-center gap-x-4 gap-y-1.5 text-[0.72rem] sm:text-xs font-semibold">
                    <button
                        type="button"
                        @click="openFaq()"
                        class="hover:underline transition-colors cursor-pointer flex items-center gap-1"
                        style="color: var(--text-secondary);"
                    >
                        <span>❓</span>
                        <span>Bantuan & FAQ</span>
                    </button>
                    <span style="color: var(--border-strong);" aria-hidden="true">•</span>
                    <button
                        type="button"
                        @click="openReportIssue()"
                        class="hover:underline transition-colors cursor-pointer flex items-center gap-1"
                        style="color: var(--text-secondary);"
                    >
                        <span>⚠️</span>
                        <span>Laporkan Masalah Pesanan</span>
                    </button>
                    <span style="color: var(--border-strong);" aria-hidden="true">•</span>
                    <button
                        type="button"
                        @click="openContact()"
                        class="hover:underline transition-colors cursor-pointer flex items-center gap-1"
                        style="color: var(--text-secondary);"
                    >
                        <span>📞</span>
                        <span>Kontak Pengelola Kantin</span>
                    </button>
                    <span style="color: var(--border-strong);" aria-hidden="true">•</span>
                    <a href="{{ route('dashboard.index') }}"
                       class="hover:underline transition-colors font-bold"
                       style="color: var(--brand);">
                        <span>Admin Panel</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Modal Dialog Interaktif untuk Bantuan, Masalah & Kontak --}}
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-950/60 backdrop-blur-xs"
            id="modal-footer-support"
        >
            <div
                @click.outside="modalOpen = false"
                class="w-full max-w-lg rounded-2xl border p-5 sm:p-6 shadow-2xl relative"
                style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card-hover);"
            >
                <div class="flex items-center justify-between mb-4 pb-2.5 border-b" style="border-color: var(--border-default);">
                    <div class="flex items-center gap-2">
                        <span class="text-xl" x-text="modalIcon"></span>
                        <h3 class="text-sm sm:text-base font-extrabold" style="color: var(--text-primary);" x-text="modalTitle"></h3>
                    </div>
                    <button
                        @click="modalOpen = false"
                        type="button"
                        class="w-7 h-7 rounded-lg border flex items-center justify-center text-xs opacity-70 hover:opacity-100 transition-opacity cursor-pointer"
                        style="border-color: var(--border-default); color: var(--text-secondary);"
                        aria-label="Tutup dialog"
                    >
                        ✕
                    </button>
                </div>

                {{-- Konten FAQ --}}
                <div x-show="activeTab === 'faq'" class="space-y-3 text-xs" style="color: var(--text-secondary);">
                    <div class="p-3 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <p class="font-bold mb-1" style="color: var(--text-primary);">Q: Bagaimana cara memesan tanpa antre?</p>
                        <p>Pilih menu makanan atau minuman dari stand kantin favorit Anda, klik <b>⚡ Beli</b> atau <b>+ Tambah ke Keranjang</b>, lalu selesaikan pemesanan. Pesanan langsung masuk ke dapur tenant kantin.</p>
                    </div>
                    <div class="p-3 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <p class="font-bold mb-1" style="color: var(--text-primary);">Q: Kapan pesanan bisa diambil?</p>
                        <p>Pesanan dapat diambil pada jam istirahat sekolah (Sesi 1: 09:30–10:00 WIB & Sesi 2: 12:00–13:00 WIB). Pantau ikon lonceng notifikasi di atas untuk melihat tanda <b>🟢 Siap Diambil</b>.</p>
                    </div>
                    <div class="p-3 rounded-xl border" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <p class="font-bold mb-1" style="color: var(--text-primary);">Q: Metode pembayaran apa saja yang diterima?</p>
                        <p>Kantin menerima pembayaran tunai di kasir stand tenant dan pembayaran non-tunai melalui scan QRIS langsung.</p>
                    </div>
                </div>

                {{-- Konten Laporkan Masalah --}}
                <div x-show="activeTab === 'report'" class="space-y-3 text-xs" style="color: var(--text-secondary);">
                    <p>Mengalami kendala dengan pesanan Anda (makanan belum siap, pesanan salah, atau kendala pembayaran)?</p>
                    <div class="p-3.5 rounded-xl border space-y-2" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <div class="flex items-center gap-2">
                            <span>📍</span>
                            <span><b>Lokasi Pos Layanan Kantin:</b> Kantor Koperasi Sekolah / Meja Kasir Utama</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>💬</span>
                            <span><b>WhatsApp Layanan Siswa:</b> 0812-9876-5432</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>⏱️</span>
                            <span><b>Waktu Layanan:</b> Senin – Jumat (07:00 – 14:00 WIB)</span>
                        </div>
                    </div>
                    <div class="text-right pt-2">
                        <a href="https://wa.me/6281298765432?text=Halo%20Pengelola%20KantinSchool,%20saya%20ingin%20melaporkan%20kendala%20pesanan"
                           target="_blank"
                           class="btn-brand text-xs px-4 py-2 rounded-xl font-bold inline-flex items-center gap-1.5 shadow-sm">
                            <span>Hubungi via WhatsApp</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- Konten Kontak Pengelola --}}
                <div x-show="activeTab === 'contact'" class="space-y-3 text-xs" style="color: var(--text-secondary);">
                    <p>Informasi resmi pengelola Kantin Sekolah Digital KantinSchool:</p>
                    <div class="p-3.5 rounded-xl border space-y-2.5" style="background-color: var(--bg-surface-2); border-color: var(--border-default);">
                        <div class="flex justify-between border-b pb-2" style="border-color: var(--border-default);">
                            <span class="font-medium">Unit Pengelola:</span>
                            <span class="font-bold" style="color: var(--text-primary);">Tim Operasional Kantin & Koperasi Sekolah</span>
                        </div>
                        <div class="flex justify-between border-b pb-2" style="border-color: var(--border-default);">
                            <span class="font-medium">Email Bantuan:</span>
                            <span class="font-bold" style="color: var(--text-primary);">kantin@sekolah.sch.id</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Jam Buka Dapur:</span>
                            <span class="font-bold" style="color: var(--text-primary);">07:00 – 14:00 WIB</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t flex justify-end" style="border-color: var(--border-default);">
                    <button
                        @click="modalOpen = false"
                        type="button"
                        class="btn-secondary text-xs px-4 py-1.5 rounded-xl font-semibold"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function footerSupportModal() {
            return {
                modalOpen: false,
                activeTab: 'faq',
                modalTitle: 'Bantuan & FAQ',
                modalIcon: '❓',
                openFaq() {
                    this.activeTab = 'faq';
                    this.modalTitle = 'Bantuan & Panduan FAQ';
                    this.modalIcon = '❓';
                    this.modalOpen = true;
                },
                openReportIssue() {
                    this.activeTab = 'report';
                    this.modalTitle = 'Laporkan Masalah Pesanan';
                    this.modalIcon = '⚠️';
                    this.modalOpen = true;
                },
                openContact() {
                    this.activeTab = 'contact';
                    this.modalTitle = 'Kontak Pengelola Kantin';
                    this.modalIcon = '📞';
                    this.modalOpen = true;
                }
            };
        }
    </script>

    @stack('modals')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
