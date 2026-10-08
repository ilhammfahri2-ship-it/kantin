<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — KantinSchool Admin</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍱</text></svg>">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite CSS/JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Anti-Flash Script --}}
    <script>
        (function () {
            var root = document.documentElement;
            var themeVersion = localStorage.getItem('ekantin_palette_ver');
            if (themeVersion !== 'wood_organic_v1') {
                localStorage.setItem('ekantin_palette_ver', 'wood_organic_v1');
                localStorage.setItem('ekantin_theme', 'light');
            }
            var stored = localStorage.getItem('ekantin_theme');
            if (stored === 'dark') {
                root.classList.add('dark');
            } else {
                root.classList.remove('dark');
            }
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(() => root.classList.remove('no-transition'), 50);
            });
        })();
    </script>
</head>
<body
    x-data="{
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('ekantin_theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('ekantin_theme', 'light');
            }
        }
    }"
    class="min-h-screen font-sans antialiased flex flex-col"
    style="color: var(--text-primary);"
>
    {{-- Ambient Effects (Aroma Hangat & Kilau Keemasan Kantin) --}}
    <div class="bg-ambient-container" aria-hidden="true">
        @include('partials.meteor-shower')
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         NAVBAR DASHBOARD TERINTEGRASI PENUH DENGAN WEB KANTIN
    ══════════════════════════════════════════════════════════════════════ --}}
    <header class="navbar relative z-30" id="dashboard-navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">
            
            {{-- Brand Logo KantinSchool + Admin Badge --}}
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2.5 shrink-0" aria-label="KantinSchool Dashboard">
                    <span class="text-2xl leading-none select-none" aria-hidden="true">🍱</span>
                    <span class="font-bold text-base tracking-tight" style="color: var(--brand);">KantinSchool</span>
                </a>
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md border tracking-wider hidden xs:inline-flex items-center gap-1"
                      style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--brand);"></span>
                    <span>Admin</span>
                </span>
            </div>

            {{-- Navigasi Desktop Terpadu --}}
            <nav class="hidden md:flex items-center gap-1 text-xs font-semibold">
                <a href="{{ route('dashboard.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('dashboard.index') 
                        ? 'background-color: var(--brand); color: #ffffff; box-shadow: 0 2px 8px -1px var(--cta-accent-glow);' 
                        : 'background-color: transparent; color: var(--text-secondary); hover:background-color: var(--bg-surface-2);' }}">
                    <span>📊</span>
                    <span>Ringkasan</span>
                </a>
                <a href="{{ route('dashboard.stock.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('dashboard.stock.*') 
                        ? 'background-color: var(--brand); color: #ffffff; box-shadow: 0 2px 8px -1px var(--cta-accent-glow);' 
                        : 'background-color: transparent; color: var(--text-secondary); hover:background-color: var(--bg-surface-2);' }}">
                    <span>🌾</span>
                    <span>Stok Bahan & Produk</span>
                </a>
                <a href="{{ route('products.index') }}"
                   class="px-3.5 py-2 rounded-xl transition-all duration-150 flex items-center gap-1.5"
                   style="{{ request()->routeIs('products.*') 
                        ? 'background-color: var(--brand); color: #ffffff; box-shadow: 0 2px 8px -1px var(--cta-accent-glow);' 
                        : 'background-color: transparent; color: var(--text-secondary); hover:background-color: var(--bg-surface-2);' }}">
                    <span>🍽️</span>
                    <span>Katalog Menu</span>
                </a>
            </nav>

            {{-- Sisi Kanan: Buka Toko, Tema, & Profil Dropdown --}}
            <div class="flex items-center gap-2 shrink-0">
                {{-- Tombol Buka Menu Toko / Pelanggan --}}
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl border transition-all duration-150 hover:scale-[1.02]"
                   style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                   title="Buka Halaman Menu Kantin (Tampilan Pelanggan)"
                >
                    <span>🏪</span>
                    <span class="hidden sm:inline">Menu Toko</span>
                    <span class="text-xs">&nearr;</span>
                </a>

                {{-- Toggle Tema Terang/Gelap --}}
                <button
                    @click="toggleTheme()"
                    class="theme-toggle"
                    :aria-label="darkMode ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
                    :title="darkMode ? 'Mode Terang' : 'Mode Gelap'"
                    id="dashboard-theme-toggle-btn"
                >
                    <svg x-show="darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>

                {{-- User Dropdown Menu --}}
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        @click.outside="open = false"
                        class="flex items-center gap-2 text-sm font-medium px-2.5 py-1.5 rounded-xl transition-colors duration-150 border"
                        style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    >
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                              style="background-color: var(--brand); color: #ffffff;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </span>
                        <span class="hidden lg:inline max-w-[7rem] truncate text-xs font-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
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
                        class="absolute right-0 mt-2 w-52 rounded-2xl py-1 z-50 origin-top-right shadow-xl backdrop-blur-md"
                        style="background-color: var(--bg-surface); border: 1px solid var(--border-default); box-shadow: var(--shadow-card-hover);"
                    >
                        <div class="px-4 py-2.5 border-b" style="border-color: var(--border-default);">
                            <p class="text-xs" style="color: var(--text-muted);">Masuk sebagai</p>
                            <p class="text-xs font-bold truncate" style="color: var(--text-primary);">{{ auth()->user()->email ?? '-' }}</p>
                            <p class="text-[11px] mt-0.5 font-semibold" style="color: var(--brand);">
                                {{ auth()->user()->isTenant() ? (auth()->user()->tenant->name ?? 'Tenant Kantin') : 'Super Admin' }}
                            </p>
                        </div>
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium transition-colors duration-100 hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <span>🍱</span>
                            <span>Menu Pelanggan</span>
                        </a>
                        <a href="{{ route('dashboard.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold transition-colors duration-100 hover:opacity-85"
                           style="color: var(--brand);">
                            <span>📊</span>
                            <span>Dashboard Ringkasan</span>
                        </a>
                        <a href="{{ route('dashboard.stock.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium transition-colors duration-100 hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <span>🌾</span>
                            <span>Manajemen Stok</span>
                        </a>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium transition-colors duration-100 hover:opacity-80"
                           style="color: var(--text-secondary);">
                            <span>🍽️</span>
                            <span>Katalog Produk</span>
                        </a>
                        <div class="border-t my-1" style="border-color: var(--border-default);"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-bold transition-colors duration-100 hover:opacity-80"
                                    style="color: var(--status-danger);">
                                <span>🚪</span>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigasi Mobile Responsif (Tampil di Layar Kecil) --}}
        <div class="md:hidden border-t px-4 py-2 flex items-center gap-2 overflow-x-auto scrollbar-none"
             style="border-color: var(--border-default); background-color: var(--bg-surface-2);">
            <a href="{{ route('dashboard.index') }}"
               class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap transition-colors"
               style="{{ request()->routeIs('dashboard.index') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                📊 Ringkasan
            </a>
            <a href="{{ route('dashboard.stock.index') }}"
               class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap transition-colors"
               style="{{ request()->routeIs('dashboard.stock.*') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                🌾 Stok Bahan
            </a>
            <a href="{{ route('products.index') }}"
               class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap transition-colors"
               style="{{ request()->routeIs('products.*') ? 'background-color: var(--brand); color: #ffffff;' : 'color: var(--text-secondary);' }}">
                🍽️ Produk
            </a>
            <a href="{{ route('home') }}"
               class="px-3 py-1.5 text-xs font-bold rounded-lg whitespace-nowrap transition-colors"
               style="color: var(--brand);">
                🏪 Menu Toko &rarr;
            </a>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════════════════════
         MAIN CONTENT DASHBOARD
    ══════════════════════════════════════════════════════════════════════ --}}
    <main class="flex-1 w-full relative z-10" id="dashboard-main-content">
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════════════════════
         FOOTER DASHBOARD TERPADU
    ══════════════════════════════════════════════════════════════════════ --}}
    <footer class="border-t py-6 mt-auto relative z-10"
            style="border-color: var(--border-default);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"
             style="color: var(--text-muted);">
            <div class="flex items-center gap-2">
                <span>🍱</span>
                <span class="font-bold" style="color: var(--brand);">KantinSchool</span>
                <span>—</span>
                <span>Panel Pengelola Dapur & Inventaris Kantin.</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="hover:underline transition-colors font-medium" style="color: var(--brand);">
                    &larr; Halaman Menu Pelanggan
                </a>
                <p>&copy; {{ date('Y') }} KantinSchool.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
