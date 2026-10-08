@extends('layouts.auth')

@section('title', 'Daftar - KantinSchool')

@section('content')
<div class="rounded-2xl p-6 sm:p-7 border shadow-sm backdrop-blur-md"
     style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold mb-1.5" style="color: var(--text-primary);">Daftar Akun Baru</h2>
        <p class="text-xs" style="color: var(--text-muted);">Buat akun agar bisa memesan menu kantin.</p>
    </div>

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors"
                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                placeholder="Nama Anda">
            @error('name')
                <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors"
                style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                placeholder="nama@email.com">
            @error('email')
                <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4" x-data="{ show: false }">
            <label for="password" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Kata Sandi</label>
            <div class="flex gap-2 items-center">
                <input type="password" x-bind:type="show ? 'text' : 'password'" name="password" id="password" required
                    class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    placeholder="Minimal 8 karakter">
                <button type="button" tabindex="-1" @click="show = !show" 
                    class="shrink-0 flex items-center justify-center w-10 h-10 border rounded-lg transition-colors hover:opacity-80"
                    style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);"
                    title="Tampilkan / Sembunyikan Sandi">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5" x-data="{ show: false }">
            <label for="password_confirmation" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Konfirmasi Kata Sandi</label>
            <div class="flex gap-2 items-center">
                <input type="password" x-bind:type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-3 py-2 text-sm rounded-lg border outline-none transition-colors"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);"
                    placeholder="Ulangi kata sandi">
                <button type="button" tabindex="-1" @click="show = !show" 
                    class="shrink-0 flex items-center justify-center w-10 h-10 border rounded-lg transition-colors hover:opacity-80"
                    style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);"
                    title="Tampilkan / Sembunyikan Sandi">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
        </div>

        <button type="submit" class="w-full btn-brand py-2.5 text-xs font-semibold uppercase tracking-wider justify-center mb-4">
            Daftar
        </button>
    </form>

    <p class="text-center text-xs" style="color: var(--text-muted);">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: var(--brand);">Masuk di sini</a>
    </p>
</div>
@endsection
