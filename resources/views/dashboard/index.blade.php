@extends('layouts.dashboard')

@section('title', 'Pesanan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header Dashboard --}}
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold tracking-tight mb-2" style="color: var(--text-primary);">
            Pesanan Masuk
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Kelola pesanan pelanggan dan perbarui status pengerjaan.
        </p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-900/50 dark:text-emerald-400 font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Statistik Card --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border shadow-sm dark:border-gray-800">
            <h3 class="text-sm font-semibold mb-1" style="color: var(--text-muted);">Pesanan Aktif</h3>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeOrdersCount }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border shadow-sm dark:border-gray-800">
            <h3 class="text-sm font-semibold mb-1" style="color: var(--text-muted);">Pesanan Selesai</h3>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $completedOrdersCount }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl p-6 border shadow-sm dark:border-gray-800">
            <h3 class="text-sm font-semibold mb-1" style="color: var(--text-muted);">Total Pendapatan</h3>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tabel Pesanan --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border shadow-sm dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-800">
            <h2 class="font-bold text-lg" style="color: var(--text-primary);">Riwayat Pesanan</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-gray-800/50">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Pesanan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Detail Menu</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Pembayaran</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: var(--text-secondary);">Status</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right" style="color: var(--text-secondary);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-gray-800">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50 dark:hover:bg-gray-800/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-mono text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ $order->order_number }}</p>
                                <p class="text-xs mt-1" style="color: var(--text-muted);">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold" style="color: var(--text-primary);">{{ $order->customer_name ?? '-' }}</p>
                                <p class="text-xs mt-1" style="color: var(--text-muted);">Kls: {{ $order->customer_class ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color: var(--text-secondary);">
                                <div class="space-y-1">
                                    @foreach($order->items as $item)
                                        <div class="flex items-start gap-2">
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $item->quantity }}x</span>
                                            <span>{{ $item->product_name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if($order->notes)
                                    <p class="mt-2 text-xs italic bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 p-2 rounded border border-amber-100 dark:border-amber-900/50">"{{ $order->notes }}"</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold" style="color: var(--text-primary);">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                <div class="mt-1">
                                    @if($order->payment_method === 'qris')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-100">QRIS</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-100">CASH</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border
                                    @if($order->status === 'pending') bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-900/50
                                    @elseif($order->status === 'confirmed') bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:border-blue-900/50
                                    @elseif($order->status === 'preparing') bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/20 dark:border-orange-900/50
                                    @elseif($order->status === 'ready') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-900/50
                                    @elseif($order->status === 'completed') bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700
                                    @elseif($order->status === 'cancelled') bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:border-red-900/50
                                    @endif
                                ">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                    @php
                                        $nextStatus = '';
                                        $btnLabel = '';
                                        $btnColor = '';
                                        
                                        if ($order->status === 'pending') {
                                            $nextStatus = 'confirmed';
                                            $btnLabel = 'Konfirmasi';
                                            $btnColor = 'bg-emerald-600 hover:bg-emerald-700 text-white';
                                        } elseif ($order->status === 'confirmed') {
                                            $nextStatus = 'preparing';
                                            $btnLabel = 'Mulai Masak';
                                            $btnColor = 'bg-emerald-600 hover:bg-emerald-700 text-white';
                                        } elseif ($order->status === 'preparing') {
                                            $nextStatus = 'ready';
                                            $btnLabel = 'Siap Diambil';
                                            $btnColor = 'bg-emerald-600 hover:bg-emerald-700 text-white';
                                        } elseif ($order->status === 'ready') {
                                            $nextStatus = 'completed';
                                            $btnLabel = 'Sudah Diambil ✔';
                                            $btnColor = 'bg-emerald-600 hover:bg-emerald-700 text-white';
                                        }
                                    @endphp

                                    <div class="flex flex-col gap-2 items-end">
                                        @if($nextStatus)
                                            <form action="{{ route('dashboard.orders.status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold transition-all shadow-sm {{ $btnColor }}">
                                                    {{ $btnLabel }} &rarr;
                                                </button>
                                            </form>
                                        @endif

                                        @if($order->status === 'pending')
                                            <form action="{{ route('dashboard.orders.status', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="text-[11px] font-semibold text-red-500 hover:text-red-700 hover:underline">
                                                    Tolak Pesanan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 dark:bg-gray-800 mb-3">
                                    <span class="text-xl">📭</span>
                                </div>
                                <h3 class="text-sm font-bold" style="color: var(--text-primary);">Belum Ada Pesanan</h3>
                                <p class="text-xs mt-1" style="color: var(--text-muted);">Pesanan dari pelanggan akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Auto-Refresh Setup (Setiap 15 Detik)
    setTimeout(() => {
        window.location.reload();
    }, 15000);

    // 2. Logika Suara Notifikasi
    let currentPending = {{ $pendingOrdersCount ?? 0 }};
    let previousPending = sessionStorage.getItem('ekantin_pending_orders');
    
    if (previousPending === null) {
        sessionStorage.setItem('ekantin_pending_orders', currentPending);
    } else {
        previousPending = parseInt(previousPending);
        if (currentPending > previousPending) {
            // Mainkan suara (Bunyi 'Ting')
            let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3'); 
            let playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.catch(e => console.log('Autoplay dicegah browser. Klik halaman dulu untuk mengizinkan suara.'));
            }
        }
        sessionStorage.setItem('ekantin_pending_orders', currentPending);
    }
});
</script>
@endsection
