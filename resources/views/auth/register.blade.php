@extends('layouts.auth')

@section('title', 'Daftar - KantinSchooll')

@section('content')
<div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-2xl p-6 shadow-sm">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Daftar Akun Baru</h2>
        <p class="text-sm" style="color: var(--text-muted);">Buat akun agar bisa memesan menu kantin.</p>
    </div>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand focus:border-brand @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand focus:border-brand @error('email') border-red-500 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4" x-data="{ show: false }">
            <label for="password" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Kata Sandi</label>
            <div class="flex gap-2 items-center">
                <input type="password" x-bind:type="show ? 'text' : 'password'" name="password" id="password" required
                    class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand focus:border-brand @error('password') border-red-500 @enderror">
                <button type="button" tabindex="-1" @click="show = !show" 
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 rounded-lg text-gray-500 hover:text-brand hover:border-brand focus:outline-none transition-all"
                    title="Tampilkan / Sembunyikan Sandi">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5" x-data="{ show: false }">
            <label for="password_confirmation" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Konfirmasi Kata Sandi</label>
            <div class="flex gap-2 items-center">
                <input type="password" x-bind:type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-brand focus:border-brand">
                <button type="button" tabindex="-1" @click="show = !show" 
                    class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 rounded-lg text-gray-500 hover:text-brand hover:border-brand focus:outline-none transition-all"
                    title="Tampilkan / Sembunyikan Sandi">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
        </div>

        <button type="submit" class="w-full btn-brand py-2 text-sm justify-center mb-3">
            Daftar
        </button>
    </form>

    <p class="text-center text-sm" style="color: var(--text-muted);">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-brand hover:underline">Masuk di sini</a>
    </p>
</div>
@endsection
