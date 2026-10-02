@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="p-2 -ml-2 rounded-lg hover:bg-slate-200/50 transition-colors" style="color: var(--text-secondary);" aria-label="Kembali ke Beranda">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Pesanan Saya</h1>
        </div>
        <a href="{{ route('home') }}" class="text-sm font-semibold hover:underline" style="color: var(--brand);">+ Pesan Lagi</a>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
            <p class="text-4xl mb-4">🛒</p>
            <h2 class="text-lg font-semibold mb-2" style="color: var(--text-primary);">Belum ada pesanan</h2>
            <p class="text-sm mb-4" style="color: var(--text-muted);">Yuk mulai pesan makanan favoritmu dari kantin!</p>
            <a href="{{ route('home') }}" class="btn-brand px-6 py-2 rounded-lg font-bold text-sm">Lihat Menu</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow-sm border p-4 sm:p-5 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b dark:border-gray-700">
                        <div>
                            <span class="text-xs font-bold px-2 py-1 rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $order->order_number }}</span>
                            <p class="text-sm font-semibold mt-2" style="color: var(--text-primary);">Kantin: {{ $order->tenant->name }}</p>
                            <p class="text-xs" style="color: var(--text-muted);">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="flex flex-col items-start sm:items-end gap-2">
                            @if($order->status === 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">Menunggu Diproses</span>
                            @elseif($order->status === 'processing')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Sedang Dimasak</span>
                            @elseif($order->status === 'ready')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 animate-pulse">Siap Diambil</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Selesai</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <span style="color: var(--text-secondary);">{{ $item->quantity }}x {{ $item->product_name }}</span>
                                <span class="font-medium" style="color: var(--text-primary);">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t dark:border-gray-700">
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">Total Belanja</span>
                        <span class="text-lg font-bold" style="color: var(--brand);">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
