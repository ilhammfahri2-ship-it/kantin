@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-12 text-center">
    <div class="mb-6">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border shadow-sm backdrop-blur-md"
             style="background-color: var(--status-success-bg); color: var(--status-success); border-color: var(--status-success-border);">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color: var(--text-primary);">
            Pesanan Berhasil!
        </h1>
        <p class="mt-2 text-sm" style="color: var(--text-secondary);">
            Terima kasih, pesananmu telah diteruskan ke tenant <span class="font-bold" style="color: var(--brand);">{{ $order->tenant->name }}</span>.
        </p>
    </div>

    <div class="rounded-2xl p-6 mb-8 text-left max-w-sm mx-auto shadow-sm border"
         style="background-color: var(--bg-surface); border-color: var(--border-default);">
        <h3 class="font-bold mb-4 border-b pb-2 text-sm" style="color: var(--text-primary); border-color: var(--border-default);">Detail Pesanan</h3>
        
        <div class="flex justify-between text-sm mb-2">
            <span style="color: var(--text-muted);">No. Pesanan:</span>
            <span class="font-mono font-semibold" style="color: var(--text-primary);">{{ $order->order_number }}</span>
        </div>

        <div class="flex justify-between text-sm mb-2">
            <span style="color: var(--text-muted);">Nama Pemesan:</span>
            <span class="font-semibold" style="color: var(--text-primary);">{{ $order->customer_name ?? '-' }}</span>
        </div>

        <div class="flex justify-between text-sm mb-4">
            <span style="color: var(--text-muted);">Kelas:</span>
            <span class="font-semibold" style="color: var(--text-primary);">{{ $order->customer_class ?? '-' }}</span>
        </div>
        
        <div class="flex justify-between text-sm items-center mb-4">
            <span style="color: var(--text-muted);">Status:</span>
            <span class="status-pill status-pill-pending">Menunggu Konfirmasi</span>
        </div>

        <div class="border-t pt-4 mt-4" style="border-color: var(--border-default);">
            <h4 class="font-bold text-xs uppercase tracking-wider mb-2" style="color: var(--text-secondary);">Metode Pembayaran:</h4>
            
            @if($order->payment_method === 'qris')
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg border"
                          style="background-color: var(--brand-subtle); color: var(--brand); border-color: var(--brand-border);">
                        QRIS Digital
                    </span>
                </div>
                <div class="bg-white p-3 rounded-xl border flex justify-center items-center mb-2 mx-auto" style="width: 260px; max-width: 100%;">
                    <img src="{{ asset('images/qris.jpg') }}?t={{ time() }}" alt="QRIS {{ $order->order_number }}" class="w-full h-auto object-contain">
                </div>
                <p class="text-center text-xs mt-1" style="color: var(--text-muted);">Tunjukkan layar ini sebagai bukti jika diminta.</p>
            @else
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg border"
                          style="background-color: var(--bg-surface-2); color: var(--text-secondary); border-color: var(--border-default);">
                        Uang Tunai (Cash)
                    </span>
                </div>
                <p class="text-xs mt-2" style="color: var(--text-muted);">Silakan lakukan pembayaran langsung ke kasir tenant <b>{{ $order->tenant->name }}</b>.</p>
            @endif
        </div>

        <div class="space-y-2 mt-4 border-t pt-4 dark:border-gray-800">
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">{{ $item->quantity }}x {{ $item->product_name }}</span>
                    <span style="color: var(--text-primary);">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        @if($order->discount > 0)
            <div class="border-t pt-3 mt-3 space-y-1.5" style="border-color: var(--border-default);">
                <div class="flex justify-between text-sm" style="color: var(--text-secondary);">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm font-semibold" style="color: var(--status-success);">
                    <span>Diskon Voucher ({{ $order->voucher_code }}):</span>
                    <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif
        
        <div class="flex justify-between text-base font-bold mt-4 pt-4 border-t dark:border-gray-800" style="color: var(--text-primary);">
            <span>Total Bayar</span>
            <span style="color: var(--brand);">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <a href="{{ route('home') }}" class="btn-brand px-6 py-2.5 rounded-lg font-medium inline-block shadow-sm">
        Kembali ke Beranda
    </a>
</div>
@endsection
