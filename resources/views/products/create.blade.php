@extends('layouts.dashboard')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
            &larr; Kembali
        </a>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Tambah Produk Baru</h1>
    </div>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Produk</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border-slate-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-emerald-500 focus:border-emerald-500 dark:text-white px-3 py-2 border shadow-sm">
                @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-lg border-slate-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-emerald-500 focus:border-emerald-500 dark:text-white px-3 py-2 border shadow-sm">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Harga (Rp)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0" step="100"
                    class="w-full rounded-lg border-slate-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-emerald-500 focus:border-emerald-500 dark:text-white px-3 py-2 border shadow-sm">
                @error('price') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stok</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" required min="0"
                    class="w-full rounded-lg border-slate-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-emerald-500 focus:border-emerald-500 dark:text-white px-3 py-2 border shadow-sm">
                @error('stock') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                <select name="category" id="category" required
                    class="w-full rounded-lg border-slate-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-emerald-500 focus:border-emerald-500 dark:text-white px-3 py-2 border shadow-sm">
                    <option value="makanan_berat" {{ old('category') == 'makanan_berat' ? 'selected' : '' }}>Makanan Berat</option>
                    <option value="makanan_ringan" {{ old('category') == 'makanan_ringan' ? 'selected' : '' }}>Makanan Ringan</option>
                    <option value="minuman" {{ old('category') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="dessert" {{ old('category') == 'dessert' ? 'selected' : '' }}>Dessert</option>
                </select>
                @error('category') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Gambar (Opsional)</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full rounded-lg border-slate-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-emerald-500 focus:border-emerald-500 dark:text-white px-3 py-1.5 border shadow-sm">
                @error('image') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}
                        class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-gray-600 dark:bg-gray-700">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Tersedia (Bisa dipesan)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                        class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-gray-600 dark:bg-gray-700">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Produk Unggulan</span>
                </label>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-200 dark:border-gray-800 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm">
                Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection
