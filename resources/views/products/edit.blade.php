@extends('layouts.dashboard')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('products.index') }}" class="p-2 rounded-lg transition-colors hover:opacity-80"
           style="background-color: var(--bg-surface-2); color: var(--text-secondary); border: 1px solid var(--border-default);">
            &larr; Kembali
        </a>
        <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Edit Produk: {{ $product->name }}</h1>
    </div>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
          class="rounded-2xl shadow-sm border p-6 sm:p-7 space-y-6"
          style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Nama Produk</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                    class="w-full rounded-lg px-3 py-2 text-sm border outline-none transition-colors"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                @error('name') <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full rounded-lg px-3 py-2 text-sm border outline-none transition-colors resize-none"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">{{ old('description', $product->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Harga (Rp)</label>
                <input type="number" name="price" id="price" value="{{ old('price', round($product->price)) }}" required min="0" step="100"
                    class="w-full rounded-lg px-3 py-2 text-sm border outline-none transition-colors"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                @error('price') <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="stock" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Stok</label>
                <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                    class="w-full rounded-lg px-3 py-2 text-sm border outline-none transition-colors"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                @error('stock') <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Kategori</label>
                <select name="category" id="category" required
                    class="w-full rounded-lg px-3 py-2 text-sm border outline-none transition-colors"
                    style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                    <option value="makanan_berat" {{ old('category', $product->category) == 'makanan_berat' ? 'selected' : '' }}>Makanan Berat</option>
                    <option value="makanan_ringan" {{ old('category', $product->category) == 'makanan_ringan' ? 'selected' : '' }}>Makanan Ringan</option>
                    <option value="minuman" {{ old('category', $product->category) == 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="dessert" {{ old('category', $product->category) == 'dessert' ? 'selected' : '' }}>Dessert</option>
                </select>
                @error('category') <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="image" class="block text-xs font-medium mb-1.5" style="color: var(--text-secondary);">Foto Produk (Kosongkan jika tetap)</label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full rounded-lg px-3 py-1.5 text-xs border outline-none transition-colors mb-2"
                    style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                @error('image') <p class="mt-1 text-xs" style="color: var(--status-danger);">{{ $message }}</p> @enderror
                
                @if($product->image)
                    <div class="mt-2 flex items-center gap-2">
                        <img src="{{ $product->image_url }}" alt="Preview" class="w-12 h-12 object-cover rounded-lg border" style="border-color: var(--border-default);">
                        <span class="text-xs" style="color: var(--text-muted);">Foto saat ini</span>
                    </div>
                @endif
            </div>

            <div class="md:col-span-2 flex flex-wrap gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                        style="accent-color: var(--brand);" class="rounded w-4 h-4">
                    <span class="text-xs font-medium" style="color: var(--text-primary);">Tersedia (Bisa dipesan)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                        style="accent-color: var(--brand);" class="rounded w-4 h-4">
                    <span class="text-xs font-medium" style="color: var(--text-primary);">Produk Unggulan (Favorit)</span>
                </label>
            </div>
        </div>

        <div class="pt-5 border-t flex justify-end gap-3" style="border-color: var(--border-default);">
            <a href="{{ route('products.index') }}" class="btn-secondary px-5 py-2 text-xs font-medium">
                Batal
            </a>
            <button type="submit" class="btn-brand px-6 py-2 text-xs font-semibold uppercase tracking-wider">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
