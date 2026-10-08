<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'KantinSchool')</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍱</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
    class="min-h-screen font-sans antialiased flex flex-col items-center justify-center relative overflow-hidden"
    style="color: var(--text-primary);"
>
    {{-- Ambient Effects (Halus & Tenang) --}}
    <div class="bg-ambient-container" aria-hidden="true">
        @include('partials.meteor-shower')
    </div>

    <div class="relative z-10 w-full max-w-sm px-4 sm:px-0 py-8">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-16 h-16 rounded-2xl border shadow-sm mb-4 transition-transform hover:scale-105"
                 style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
                <span class="text-3xl">🍱</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight" style="color: var(--brand);">KantinSchool</h1>
            <p class="mt-1.5 text-xs sm:text-sm" style="color: var(--text-secondary);">Pesan makanan kantin cepat tanpa antre.</p>
        </div>

        @yield('content')

        <div class="mt-8 text-center flex items-center justify-center gap-4 text-xs">
            <a href="{{ route('home') }}" class="hover:underline transition-colors" style="color: var(--text-muted);">
                &larr; Beranda
            </a>
            <span style="color: var(--border-strong);">•</span>
            <a href="{{ route('dashboard.index') }}" class="hover:underline transition-colors" style="color: var(--text-muted);">
                Panel Tenant / Admin
            </a>
        </div>
    </div>
</body>
</html>
