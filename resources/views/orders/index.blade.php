@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="p-2 -ml-2 rounded-lg transition-colors hover:opacity-80" style="color: var(--text-secondary); background: var(--bg-surface-2);" aria-label="Kembali ke Beranda">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">Pesanan Saya</h1>
        </div>
        <a href="{{ route('home') }}" class="btn-brand text-xs">
            + Pesan Lagi
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-16 rounded-2xl shadow-sm border" style="background-color: var(--bg-surface); border-color: var(--border-default);">
            <p class="text-4xl mb-4">🛒</p>
            <h2 class="text-lg font-semibold mb-2" style="color: var(--text-primary);">Belum ada pesanan</h2>
            <p class="text-sm mb-6" style="color: var(--text-muted);">Yuk mulai pesan makanan favoritmu dari kantin!</p>
            <a href="{{ route('home') }}" class="btn-brand px-6 py-2.5 rounded-lg text-xs font-semibold">Lihat Menu</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="rounded-2xl shadow-sm border p-4 sm:p-5 transition-all duration-200"
                     style="background-color: var(--bg-surface); border-color: var(--border-default); box-shadow: var(--shadow-card);">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b"
                         style="border-color: var(--border-default);">
                        <div>
                            <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-md border"
                                  style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                                {{ $order->order_number }}
                            </span>
                            <p class="text-sm font-semibold mt-2.5" style="color: var(--text-primary);">Tenant: {{ $order->tenant->name }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--text-muted);">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <div class="flex flex-col items-start sm:items-end gap-2">
                            @if($order->status === 'pending')
                                <span class="status-pill status-pill-pending">Menunggu Diproses</span>
                            @elseif($order->status === 'processing' || $order->status === 'preparing')
                                <span class="status-pill status-pill-processing">Sedang Dimasak</span>
                            @elseif($order->status === 'ready')
                                <span class="status-pill status-pill-ready">Siap Diambil</span>
                            @elseif($order->status === 'cancelled')
                                <span class="status-pill status-pill-cancelled">Dibatalkan</span>
                            @else
                                <span class="status-pill status-pill-completed">Selesai</span>
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

                    <div class="flex items-center justify-between pt-3 border-t" style="border-color: var(--border-default);">
                        <span class="text-sm font-medium" style="color: var(--text-secondary);">Total Belanja</span>
                        <span class="text-base font-bold" style="color: var(--brand);">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
