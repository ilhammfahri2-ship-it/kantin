@extends('layouts.dashboard')

@section('title', 'Kelola Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Daftar Produk</h1>
        <a href="{{ route('products.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm">
            + Tambah Produk
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-gray-800 border-b border-slate-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">Produk</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">Kategori</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">Harga</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">Stok</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300">Status</th>
                        <th class="px-6 py-4 font-semibold text-slate-600 dark:text-slate-300 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                    @forelse ($products as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover border border-slate-200 dark:border-gray-700">
                                    <div class="font-medium text-slate-800 dark:text-white">{{ $product->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $product->category_label }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800 dark:text-white">{{ $product->formatted_price }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $product->stock }}</td>
                            <td class="px-6 py-4">
                                @if($product->is_available)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Tidak Tersedia
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">Edit</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                Belum ada produk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
