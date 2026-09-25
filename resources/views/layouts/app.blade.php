<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── SEO ─────────────────────────────────────────────────────────── --}}
    <title>@yield('title', 'e-Kantin') — Pesan Makanan Tanpa Antre</title>
    <meta name="description" content="@yield('meta_description', 'Pesan makanan dari kantin favoritmu secara digital, mudah, cepat, tanpa harus mengantri.')">

    {{-- ── Favicon (SVG inline, tidak perlu file eksternal) ───────────── --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍱</text></svg>">

    {{-- ── Google Fonts: Inter ─────────────────────────────────────────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- ── Vite: Tailwind CSS + App JS ───────────────────────────────── --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ── Alpine.js (CDN — di head agar x-cloak bekerja) ────────────── --}}
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
            } else if (stored === 'light') {
                applyTheme(false);
            } else {
                // Belum ada preferensi tersimpan → ikuti preferensi OS
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                applyTheme(prefersDark);
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
    style="background-color: var(--bg-canvas); color: var(--text-primary);"
>

    {{-- ══════════════════════════════════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════════════════════════════════════ --}}
    <header class="navbar" id="main-navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

            {{-- Brand Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0" aria-label="e-Kantin Beranda">
                <span class="text-2xl leading-none select-none" aria-hidden="true">🍱</span>
                <span class="font-bold text-base tracking-tight" style="color: var(--brand);">e-Kantin</span>
            </a>

            {{-- Navigasi Tengah (Desktop) --}}
            <nav class="hidden md:flex items-center gap-6" aria-label="Navigasi utama">
                <a href="{{ route('home') }}"
                   class="text-sm font-medium transition-colors duration-150 hover:opacity-80 {{ request()->routeIs('home') ? 'text-brand font-semibold' : '' }}"
                   style="{{ request()->routeIs('home') ? 'color: var(--brand);' : 'color: var(--text-secondary);' }}"
                >
                    Menu
                </a>
                {{-- Tambahkan link navigasi lain di sini (Riwayat Pesanan, dll.) --}}
            </nav>

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
                    {{-- Badge jumlah item --}}
                    <span
                        x-show="cartCount > 0"
                        x-text="cartCount > 99 ? '99+' : cartCount"
                        class="absolute -top-1.5 -right-1.5 min-w-[1.125rem] h-[1.125rem] px-1 text-[0.625rem] font-bold leading-none rounded-full flex items-center justify-center"
                        style="background-color: var(--brand); color: #fff;"
                    ></span>
                </a>

                {{-- Auth Links --}}
                @guest
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium px-3 py-2 rounded-lg transition-colors duration-150"
                       style="color: var(--text-secondary);"
                    >Masuk</a>
                    <a href="{{ route('register') }}" class="btn-brand">Daftar</a>
                @else
                    {{-- User dropdown (Alpine) --}}
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            class="flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg transition-colors duration-150"
                            style="background-color: var(--bg-surface-2); color: var(--text-primary);"
                        >
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                                  style="background-color: var(--brand); color: #fff;">
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
                            class="absolute right-0 mt-2 w-48 rounded-xl py-1 z-50 origin-top-right"
                            style="background-color: var(--bg-surface); border: 1px solid var(--border-default); box-shadow: var(--shadow-card-hover);"
                        >
                            <div class="px-4 py-2.5 border-b" style="border-color: var(--border-default);">
                                <p class="text-xs" style="color: var(--text-muted);">Masuk sebagai</p>
                                <p class="text-sm font-medium truncate" style="color: var(--text-primary);">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors duration-100 hover:opacity-75"
                               style="color: var(--text-secondary);">
                                Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-sm transition-colors duration-100 hover:opacity-75"
                                        style="color: #ef4444;">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════════════════════
         FLASH MESSAGES
    ══════════════════════════════════════════════════════════════════════ --}}
    @if (session('success') || session('error') || session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4" role="alert">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm"
                     style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm"
                     style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
                </div>
            @endif
            @if (session('info'))
                <div x-data="{ show: true }" x-show="show" x-transition
                     class="flex items-start gap-3 p-4 rounded-xl mb-3 text-sm"
                     style="background-color: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
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
    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    {{-- ══════════════════════════════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════════════════════════════════ --}}
    <footer class="mt-16 border-t" style="border-color: var(--border-default);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm" style="color: var(--text-muted);">
                <div class="flex items-center gap-2">
                    <span>🍱</span>
                    <span class="font-semibold" style="color: var(--brand);">e-Kantin</span>
                    <span>—</span>
                    <span>Pesan makanan tanpa antre.</span>
                </div>
                <p>© {{ date('Y') }} e-Kantin. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
