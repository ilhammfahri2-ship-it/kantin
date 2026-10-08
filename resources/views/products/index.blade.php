@extends('layouts.dashboard')

@section('title', 'Kelola Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Daftar Produk</h1>
            <p class="text-xs sm:text-sm mt-0.5" style="color: var(--text-secondary);">Kelola seluruh menu makanan dan minuman tenant.</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn-brand text-xs font-semibold">
            + Tambah Produk
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl border text-sm font-medium backdrop-blur-md"
             style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl shadow-sm border overflow-hidden"
         style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="border-b text-xs font-bold uppercase tracking-wider"
                       style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-secondary);">
                    <tr>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4">Stok</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-default);">
                    @forelse ($products as $product)
                        <tr class="transition-colors hover:opacity-90" style="border-color: var(--border-default);">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->image)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover border" style="border-color: var(--border-default);">
                                    @else
                                        <div class="w-10 h-10 rounded-lg border flex items-center justify-center text-xs" style="background-color: var(--bg-surface-2); border-color: var(--border-default); color: var(--text-muted);">
                                            🍱
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-sm" style="color: var(--text-primary);">{{ $product->name }}</div>
                                        @if($product->is_featured)
                                            <span class="text-[10px] font-bold uppercase tracking-wider" style="color: var(--brand);">★ Unggulan</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4" style="color: var(--text-secondary);">{{ $product->category_label }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST" class="flex items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="price" value="{{ (int) $product->price }}" min="0" step="500" class="w-24 px-2 py-1 text-xs font-bold rounded-lg border outline-none" style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                                    <button type="submit" class="btn-secondary text-[11px] font-bold px-2 py-1 rounded-lg border">Ubah</button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('dashboard.products.quick-update', $product->id) }}" method="POST" class="flex items-center gap-1.5">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="stock" value="{{ $product->stock }}" min="0" class="w-16 px-2 py-1 text-xs font-black text-center rounded-lg border outline-none" style="background-color: var(--bg-surface-2); color: var(--text-primary); border-color: var(--border-default);">
                                    <button type="submit" class="text-[11px] font-bold px-2.5 py-1 rounded-lg border {{ $product->stock <= 0 ? 'btn-brand shadow-sm' : 'btn-secondary' }}">
                                        {{ $product->stock <= 0 ? 'Isi!' : 'Simpan' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->stock <= 0)
                                    <span class="status-pill status-pill-cancelled">Habis (0)</span>
                                @elseif($product->is_available)
                                    <span class="status-pill status-pill-ready">Tersedia</span>
                                @else
                                    <span class="status-pill status-pill-completed">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3 text-xs">
                                    <a href="{{ route('products.edit', $product->id) }}" class="font-semibold transition-opacity hover:opacity-80" style="color: var(--brand);">
                                        Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold transition-opacity hover:opacity-80" style="color: var(--status-danger);">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center" style="color: var(--text-muted);">
                                Belum ada produk. Klik tombol Tambah Produk untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="px-6 py-4 border-t" style="border-color: var(--border-default);">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
