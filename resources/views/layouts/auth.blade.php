<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'KantinSchooll')</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍱</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        (function () {
            var root = document.documentElement;
            var stored = localStorage.getItem('ekantin_theme');
            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
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
    style="background-color: var(--bg-canvas); color: var(--text-primary);"
>
    {{-- Ambient Effects --}}
    <div class="bg-ambient-container" aria-hidden="true">
        <div class="glow-orb glow-orb-emerald"></div>
        <div class="glow-orb glow-orb-indigo"></div>
        <div class="bg-grid-overlay"></div>
    </div>

    <div class="relative z-10 w-full max-w-sm px-4 sm:px-0">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white dark:bg-gray-900 border dark:border-gray-800 shadow-sm mb-4">
                <span class="text-3xl">🍱</span>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight" style="color: var(--brand);">KantinSchooll</h1>
            <p class="mt-2 text-sm" style="color: var(--text-secondary);">Pesan makanan kantin dengan mudah.</p>
        </div>

        @yield('content')

        <div class="mt-8 text-center">
            <a href="{{ route('dashboard.index') }}" class="text-xs font-medium hover:underline" style="color: var(--text-muted);">
                Masuk ke Panel Tenant / Admin &rarr;
            </a>
        </div>
    </div>
</body>
</html>
