<!DOCTYPE html>
<html lang="id" class="no-transition">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — KantinSchooll Admin</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite CSS/JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
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
    class="min-h-screen font-sans antialiased bg-slate-50 dark:bg-[#090D16] text-slate-900 dark:text-slate-100 flex flex-col"
>

    {{-- Navbar Dashboard Khusus --}}
    <header class="bg-white dark:bg-gray-900 border-b border-slate-200 dark:border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2">
                    <span class="text-2xl">📊</span>
                    <span class="font-bold text-lg text-emerald-600 dark:text-emerald-400 tracking-tight">Kantin Admin</span>
                </a>
                <span class="hidden sm:inline-block w-px h-6 bg-slate-200 dark:bg-gray-700 mx-2"></span>
                <nav class="hidden sm:flex items-center gap-4 text-sm font-medium">
                    <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400' }}">Ringkasan</a>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400' }}">Produk</a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST" class="m-0 flex">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors shadow-sm">
                        Keluar
                    </button>
                </form>

                {{-- Toggle Theme ("Tombol Mata") --}}
                <button @click="toggleTheme()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-gray-800 transition-colors">
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>

                <span class="w-px h-5 bg-slate-200 dark:bg-gray-700 hidden sm:block"></span>

                <div class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    {{ auth()->user()->name ?? 'Akun Admin' }}
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 w-full relative">
        @yield('content')
    </main>

    {{-- Footer Dashboard --}}
    <footer class="border-t border-slate-200 dark:border-gray-800 bg-white dark:bg-gray-900 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-slate-500 dark:text-slate-400">
            &copy; {{ date('Y') }} KantinSchooll Admin Panel.
        </div>
    </footer>

</body>
</html>
