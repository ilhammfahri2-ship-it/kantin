@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-12 text-center">
    <div class="mb-6">
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold tracking-tight" style="color: var(--text-primary);">
            Pesanan Berhasil!
        </h1>
        <p class="mt-2 text-sm" style="color: var(--text-secondary);">
            Terima kasih, pesananmu telah diteruskan ke tenant <span class="font-bold">{{ $order->tenant->name }}</span>.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-xl p-6 mb-8 text-left max-w-sm mx-auto shadow-sm">
        <h3 class="font-bold mb-4 border-b pb-2 dark:border-gray-800" style="color: var(--text-primary);">Detail Pesanan</h3>
        
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
        
        <div class="flex justify-between text-sm mb-4">
            <span style="color: var(--text-muted);">Status:</span>
            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-bold rounded">Menunggu Konfirmasi</span>
        </div>

        <div class="border-t pt-4 mt-4 dark:border-gray-800">
            <h4 class="font-bold text-sm mb-2" style="color: var(--text-primary);">Metode Pembayaran:</h4>
            
            @if($order->payment_method === 'qris')
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded">QRIS</span>
                </div>
                <div class="bg-white p-3 rounded-xl border flex justify-center items-center mb-2 mx-auto" style="width: 300px; max-width: 100%;">
                    <img src="{{ asset('images/qris.jpg') }}?t={{ time() }}" alt="QRIS {{ $order->order_number }}" class="w-full h-auto object-contain">
                </div>
                <p class="text-center text-xs" style="color: var(--text-muted);">Tunjukkan layar ini sebagai bukti jika diminta.</p>
            @else
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 text-xs font-bold rounded">Uang Tunai (Cash)</span>
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
        
        <div class="flex justify-between text-base font-bold mt-4 pt-4 border-t dark:border-gray-800" style="color: var(--text-primary);">
            <span>Total</span>
            <span style="color: var(--brand);">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <a href="{{ route('home') }}" class="btn-brand px-6 py-2.5 rounded-lg font-medium inline-block shadow-sm">
        Kembali ke Beranda
    </a>
</div>
@endsection
