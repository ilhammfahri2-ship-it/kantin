@extends('layouts.app')

@section('title', 'Keranjang Belanja')
@section('meta_description', 'Tinjau dan konfirmasi pesananmu sebelum checkout.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8" x-data="cartPage()">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('home') }}" class="p-2 -ml-2 rounded-lg hover:bg-slate-200/50 transition-colors" style="color: var(--text-secondary);" aria-label="Kembali ke Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold tracking-tight" style="color: var(--text-primary);">
            Keranjang Belanja
        </h1>
    </div>

    {{-- State: Keranjang Kosong --}}
    <div x-show="items.length === 0" class="text-center py-16">
        <p class="text-5xl mb-4">🛒</p>
        <h2 class="text-lg font-semibold mb-1" style="color: var(--text-primary);">Keranjangmu masih kosong</h2>
        <p class="text-sm mb-6" style="color: var(--text-muted);">Yuk, pilih menu dari katalog kantin.</p>
        <a href="{{ route('home') }}" class="btn-brand">Lihat Menu</a>
    </div>

    {{-- State: Ada Item --}}
    <div x-show="items.length > 0">

        {{-- Status Kantin Tutup --}}
        @if(!$isOpen)
        <div class="mb-5 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 border shadow-sm"
             style="background-color: var(--status-danger-bg); border-color: var(--status-danger-border);">
            <span class="text-2xl mt-0.5">🛑</span>
            <div>
                <h2 class="font-bold text-sm sm:text-base" style="color: var(--status-danger);">Kantin Sedang Tutup</h2>
                <p class="text-xs sm:text-sm mt-1 leading-relaxed" style="color: var(--text-secondary);">
                    Pemesanan hanya dibuka saat jam istirahat:
                    <b>Istirahat 1 (09:30 - 10:00 WIB)</b> & <b>Istirahat 2 (12:00 - 13:00 WIB)</b>.
                </p>
                <p class="text-xs mt-1.5 font-medium" style="color: var(--text-muted);">
                    Saat ini pukul {{ $schedule['currentTime'] }} WIB. Pemesanan dibuka kembali: <b style="color: var(--brand);">{{ $schedule['nextSession'] }}</b>.
                </p>
            </div>
        </div>
        @endif

        {{-- Info Tenant --}}
        <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background-color: var(--bg-surface-2);">
            <span style="color: var(--text-muted);">Pesanan dari:</span>
            <span class="font-semibold ml-1" style="color: var(--brand);" x-text="tenantNames.length === 1 ? tenantNames[0] : tenantNames.length + ' kantin berbeda'"></span>
        </div>

        {{-- Daftar Item --}}
        <div class="rounded-xl overflow-hidden border mb-4" style="border-color: var(--border-default);">
            <template x-for="(item, id) in itemsMap" :key="id">
                <div class="flex items-center gap-4 px-4 py-3.5 border-b last:border-b-0"
                     style="border-color: var(--border-default); background-color: var(--bg-surface);">
                    <template x-if="item.image">
                        <img :src="item.image" :alt="item.name" class="w-10 h-10 rounded-lg object-cover border shrink-0" style="border-color: var(--border-default);">
                    </template>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate text-gray-900 dark:text-white" x-text="item.name"></p>
                        <p class="text-xs mt-0.5 text-gray-500 dark:text-gray-400"
                           x-text="'Rp ' + item.price.toLocaleString('id-ID') + ' / porsi'"></p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button @click="decrease(id)"
                                class="w-7 h-7 rounded-lg border text-lg leading-none transition-colors duration-100 hover:opacity-70 text-gray-900 dark:text-white"
                                style="border-color: var(--border-default); background: var(--bg-surface-2);"
                                :aria-label="'Kurangi ' + item.name">−</button>
                        <span class="w-6 text-center text-sm font-semibold text-gray-900 dark:text-white"
                              x-text="item.quantity"></span>
                        <button @click="increase(id)"
                                class="w-7 h-7 rounded-lg border text-lg leading-none transition-colors duration-100 hover:opacity-70 text-gray-900 dark:text-white"
                                style="border-color: var(--border-default); background: var(--bg-surface-2);"
                                :aria-label="'Tambah ' + item.name">+</button>
                    </div>
                    <div class="flex flex-col items-end shrink-0 gap-1">
                        <span class="text-sm font-bold w-24 text-right shrink-0 text-gray-900 dark:text-white"
                              x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
                        <button @click="removeItem(id)" 
                                class="text-xs flex items-center gap-1 transition-opacity hover:opacity-80"
                                style="color: var(--status-danger);"
                                aria-label="Batalkan Pesanan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Ringkasan & Voucher Promo --}}
        <div class="rounded-xl border p-4 mb-6 shadow-sm" style="border-color: var(--border-default); background-color: var(--bg-surface);">
            {{-- Kartu Status Voucher Promo Aktif --}}
            <template x-if="activeVoucher">
                <div class="mb-3.5 p-3 rounded-xl border border-dashed border-emerald-600/50 bg-emerald-50/70 dark:bg-emerald-950/30 flex items-center justify-between text-xs sm:text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🎟️</span>
                        <div>
                            <span class="font-bold text-emerald-800 dark:text-emerald-300" x-text="'Voucher ' + activeVoucher.code + ' Aktif'"></span>
                            <span class="block text-[0.7rem] text-emerald-700/80 dark:text-emerald-400" x-text="'Diskon ' + activeVoucher.discount_percent + '% diterapkan pada pesanan ini'"></span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-black bg-emerald-600 text-white" x-text="'-' + activeVoucher.discount_percent + '%'"></span>
                </div>
            </template>

            {{-- Opsi Klaim Kupon di Keranjang jika Belum Ada Voucher Aktif --}}
            <template x-if="!activeVoucher">
                <div class="mb-3.5 p-2.5 sm:p-3 rounded-xl border border-dashed flex items-center justify-between gap-2 text-xs"
                     style="border-color: var(--brand-border); background-color: var(--bg-surface-2);">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🎟️</span>
                        <div>
                            <span class="font-bold" style="color: var(--brand);">Kupon KANTINHEMAT (Diskon 20%)</span>
                            <span class="block text-[0.68rem]" style="color: var(--text-muted);">Klaim kupon untuk hemat belanja</span>
                        </div>
                    </div>
                    <button type="button" @click="claimCartVoucher()" :disabled="claimingVoucher" class="btn-brand text-xs px-2.5 py-1 rounded-lg font-bold shadow-xs">
                        <span x-text="claimingVoucher ? '...' : 'Klaim 20%'"></span>
                    </button>
                </div>
            </template>

            <div class="flex justify-between text-sm mb-2" style="color: var(--text-secondary);">
                <span>Subtotal (<span x-text="totalItems"></span> item)</span>
                <span x-text="'Rp ' + subtotal.toLocaleString('id-ID')"></span>
            </div>

            {{-- Baris Diskon Voucher 20% --}}
            <template x-if="activeVoucher && discount > 0">
                <div class="flex justify-between text-sm mb-2 font-semibold" style="color: var(--status-success);">
                    <span class="flex items-center gap-1.5">
                        <span>🎟️ Diskon Voucher (<span x-text="activeVoucher.code"></span>)</span>
                    </span>
                    <span x-text="'-Rp ' + discount.toLocaleString('id-ID')"></span>
                </div>
            </template>

            <div class="border-t pt-3 mt-2 flex justify-between font-bold" style="border-color: var(--border-default);">
                <span style="color: var(--text-primary);">Total Bayar</span>
                <span style="color: var(--brand);" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
            </div>
        </div>

        {{-- Data Pembeli --}}
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="customer-name" class="block text-sm font-medium" style="color: var(--text-secondary);">
                        Nama Pemesan <span style="color: var(--status-danger);">*</span>
                    </label>
                    <template x-if="isLoggedIn">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md"
                              style="background-color: var(--brand-subtle); color: var(--brand);">
                            🔒 Akun Login
                        </span>
                    </template>
                </div>
                <input
                    type="text"
                    id="customer-name"
                    name="customer_name"
                    x-model="customerName"
                    required
                    :readonly="isLoggedIn"
                    :class="isLoggedIn ? 'cursor-not-allowed opacity-90' : ''"
                    placeholder="Contoh: Budi Santoso"
                    class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none transition-colors duration-150"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                >
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="customer-class" class="block text-sm font-medium" style="color: var(--text-secondary);">
                        Kelas <span style="color: var(--status-danger);">*</span>
                    </label>
                    <template x-if="isLoggedIn">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md"
                              style="background-color: var(--brand-subtle); color: var(--brand);">
                            🔒 Akun Login
                        </span>
                    </template>
                </div>
                <input
                    type="text"
                    id="customer-class"
                    name="customer_class"
                    x-model="customerClass"
                    required
                    :readonly="isLoggedIn"
                    :class="isLoggedIn ? 'cursor-not-allowed opacity-90' : ''"
                    placeholder="Contoh: 10 IPA 1"
                    class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none transition-colors duration-150"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                >
            </div>
        </div>

        {{-- Catatan --}}
        <div class="mb-6">
            <label for="cart-notes" class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">
                Catatan Pesanan (opsional)
            </label>
            <textarea
                id="cart-notes"
                x-model="notes"
                rows="2"
                placeholder="Contoh: tidak pedas, porsi banyak, dll."
                class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none resize-none transition-colors duration-150"
                style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
            ></textarea>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="mb-8">
            <label class="block text-sm font-medium mb-3" style="color: var(--text-secondary);">
                Metode Pembayaran <span style="color: var(--status-danger);">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all duration-150"
                       :style="paymentMethod === 'cash' ? 'border-color: var(--brand); background-color: var(--brand-subtle);' : 'border-color: var(--border-default); background-color: var(--bg-surface);'">
                    <input type="radio" x-model="paymentMethod" value="cash" style="accent-color: var(--brand);" class="w-4 h-4">
                    <div>
                        <span class="block text-sm font-bold" style="color: var(--text-primary);">Uang Tunai (Cash)</span>
                        <span class="block text-xs" style="color: var(--text-muted);">Bayar di kasir kantin</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all duration-150"
                       :style="paymentMethod === 'qris' ? 'border-color: var(--brand); background-color: var(--brand-subtle);' : 'border-color: var(--border-default); background-color: var(--bg-surface);'">
                    <input type="radio" x-model="paymentMethod" value="qris" style="accent-color: var(--brand);" class="w-4 h-4">
                    <div>
                        <span class="block text-sm font-bold text-gray-900 dark:text-white">QRIS</span>
                        <span class="block text-xs" style="color: var(--text-muted);">Pembayaran Digital</span>
                    </div>
                </label>
            </div>
            
            {{-- Muncul foto QRIS jika dipilih --}}
            <div x-show="paymentMethod === 'qris'" x-transition class="mt-4">
                <div class="p-5 rounded-xl border flex flex-col items-center justify-center shadow-sm"
                     style="background-color: var(--bg-surface); border-color: var(--border-default);">
                    <span class="font-bold mb-3 text-sm tracking-wide" style="color: var(--brand);">Scan QRIS di Bawah Ini</span>
                    <div class="p-3 bg-white rounded-xl flex justify-center items-center shadow-sm mb-3 border border-gray-100 mx-auto" style="width: 280px; max-width: 100%;">
                        <img src="{{ asset('images/qris.jpg') }}?t={{ time() }}" alt="Foto QRIS" class="w-full h-auto object-contain">
                    </div>
                    <span class="text-xs text-center leading-relaxed" style="color: var(--text-secondary);">
                        Pastikan untuk mentransfer sesuai <b>Total</b>, lalu tekan <b>Pesan Sekarang</b>.
                    </span>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="clearCart()"
                    class="flex-1 py-2.5 text-sm font-medium rounded-lg border transition-colors duration-150 hover:opacity-70"
                    style="border-color: var(--border-default); color: var(--text-secondary); background: transparent;">
                Kosongkan
            </button>
            <a href="{{ route('home') }}"
               class="flex-1 py-2.5 text-sm font-medium rounded-lg border text-center transition-colors duration-150 hover:opacity-70"
               style="border-color: var(--border-default); color: var(--text-secondary); background: transparent;">
                + Tambah Menu
            </a>
            @if($isOpen)
                <button
                    class="flex-2 py-3 px-6 btn-brand rounded-xl text-sm font-extrabold justify-center shadow-md transition-all hover:scale-[1.02] active:scale-95"
                    @click="checkout()"
                    id="btn-checkout"
                >
                    Pesan Sekarang
                </button>
            @else
                <button
                    type="button"
                    disabled
                    class="flex-2 py-2.5 px-6 rounded-lg text-sm justify-center font-bold opacity-60 cursor-not-allowed border"
                    style="background-color: var(--bg-surface-2); color: var(--text-muted); border-color: var(--border-default);"
                    title="Kantin sedang tutup di luar jam istirahat"
                >
                    Kantin Tutup
                </button>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function cartPage() {
        return {
            itemsMap: {},
            notes: '',
            isLoggedIn: @json(auth()->check()),
            customerName: @json(auth()->check() ? auth()->user()->name : ''),
            customerClass: @json(auth()->check() ? (auth()->user()->display_classroom ?? auth()->user()->classroom ?? '') : ''),
            paymentMethod: 'cash',
            activeVoucher: @json($activeVoucher ?? null),

            get items() {
                return Object.entries(this.itemsMap);
            },
            get tenantNames() {
                const names = Object.values(this.itemsMap).map(i => i.tenantName);
                return [...new Set(names)].filter(Boolean);
            },
            get totalItems() {
                return Object.values(this.itemsMap).reduce((s, i) => s + i.quantity, 0);
            },
            get subtotal() {
                return Object.values(this.itemsMap).reduce((s, i) => s + (i.price * i.quantity), 0);
            },
            get discount() {
                if (!this.activeVoucher || !this.activeVoucher.discount_percent) return 0;
                return Math.round((this.subtotal * this.activeVoucher.discount_percent) / 100);
            },
            get total() {
                return Math.max(0, this.subtotal - this.discount);
            },

            init() {
                this.itemsMap = window.getCart ? window.getCart() : {};
                if (!this.isLoggedIn) {
                    const savedName = localStorage.getItem('ekantin_customer_name') || '';
                    const savedClass = localStorage.getItem('ekantin_customer_class') || '';
                    if (savedName) this.customerName = savedName;
                    if (savedClass) this.customerClass = savedClass;
                }
                if (!this.activeVoucher) {
                    const saved = sessionStorage.getItem('ekantin_active_voucher');
                    if (saved) {
                        try {
                            this.activeVoucher = JSON.parse(saved);
                        } catch(e) {}
                    }
                }
            },

            claimingVoucher: false,

            async claimCartVoucher() {
                if (this.claimingVoucher) return;
                this.claimingVoucher = true;

                try {
                    const res = await fetch('{{ route("voucher.claim") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ code: 'KANTINHEMAT' })
                    });
                    const data = await res.json();

                    if (res.ok && data.success) {
                        this.activeVoucher = data.voucher;
                        sessionStorage.setItem('ekantin_active_voucher', JSON.stringify(data.voucher));
                        Swal.fire({
                            title: 'Diskon 20% Aktif! 🎟️',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#D97706',
                            confirmButtonText: 'Mantap'
                        });
                    } else if (data.require_login) {
                        Swal.fire({
                            title: 'Perlu Masuk Akun 🔐',
                            text: data.message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#D97706',
                            cancelButtonColor: '#78716C',
                            confirmButtonText: 'Masuk Sekarang',
                            cancelButtonText: 'Nanti'
                        }).then((r) => {
                            if (r.isConfirmed) {
                                window.location.href = data.login_url || '{{ route("login") }}';
                            }
                        });
                    } else {
                        Swal.fire({
                            title: data.expired ? 'Voucher Kedaluwarsa' : (data.already_claimed ? 'Klaim Ditolak' : 'Gagal Klaim'),
                            text: data.message,
                            icon: data.already_claimed ? 'info' : 'error',
                            confirmButtonColor: '#D97706',
                            confirmButtonText: 'Tutup'
                        });
                        if (data.already_claimed && data.voucher) {
                            this.activeVoucher = data.voucher;
                        }
                    }
                } catch (e) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal menghubungi server.',
                        icon: 'error',
                        confirmButtonColor: '#D97706'
                    });
                } finally {
                    this.claimingVoucher = false;
                }
            },

            save() {
                sessionStorage.setItem('ekantin_cart', JSON.stringify(this.itemsMap));
                window.refreshCartBadge && window.refreshCartBadge();
            },

            increase(id) {
                this.itemsMap[id].quantity++;
                this.itemsMap = { ...this.itemsMap }; // trigger Alpine reactivity
                this.save();
            },

            decrease(id) {
                if (this.itemsMap[id].quantity > 1) {
                    this.itemsMap[id].quantity--;
                } else {
                    delete this.itemsMap[id];
                }
                this.itemsMap = { ...this.itemsMap }; // trigger Alpine reactivity
                this.save();
            },

            removeItem(id) {
                Swal.fire({
                    title: 'Batalkan pesanan?',
                    text: 'Yakin ingin membatalkan pesanan item ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, batalkan!',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        delete this.itemsMap[id];
                        this.itemsMap = { ...this.itemsMap }; // trigger Alpine reactivity
                        this.save();
                        Swal.fire({
                            title: 'Dibatalkan!',
                            text: 'Pesanan berhasil dibatalkan.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            },

            clearCart() {
                Swal.fire({
                    title: 'Kosongkan keranjang?',
                    text: 'Yakin ingin mengosongkan keranjang belanja Anda?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, kosongkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.itemsMap = {};
                        sessionStorage.removeItem('ekantin_cart');
                        window.refreshCartBadge && window.refreshCartBadge();
                        Swal.fire({
                            title: 'Dikosongkan!',
                            text: 'Keranjang telah dikosongkan.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            },

            checkout() {
                if (Object.keys(this.itemsMap).length === 0) return;
                
                const items = Object.entries(this.itemsMap).map(([id, item]) => ({
                    id: id,
                    quantity: item.quantity
                }));
                const tenantId = Object.values(this.itemsMap)[0].tenantId;

                if (!this.customerName.trim() || !this.customerClass.trim()) {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Mohon isi Nama Pemesan dan Kelas terlebih dahulu.',
                        icon: 'warning'
                    });
                    return;
                }

                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = 'Memproses...';
                btn.disabled = true;

                fetch('{{ route("checkout.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: items,
                        notes: this.notes,
                        customer_name: this.customerName,
                        customer_class: this.customerClass,
                        payment_method: this.paymentMethod,
                        voucher_code: this.activeVoucher ? this.activeVoucher.code : null
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.itemsMap = {};
                        sessionStorage.removeItem('ekantin_cart');
                        sessionStorage.removeItem('ekantin_active_voucher');
                        if(window.refreshCartBadge) window.refreshCartBadge();
                        window.location.href = data.redirect_url;
                    } else {
                        Swal.fire({
                            title: 'Terjadi Kesalahan!',
                            text: data.message || 'Terjadi kesalahan saat checkout.',
                            icon: 'error'
                        });
                        btn.textContent = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal memproses pesanan. Periksa koneksi internet Anda.',
                        icon: 'error'
                    });
                    btn.textContent = originalText;
                    btn.disabled = false;
                });
            },
        };
    }
</script>
@endpush
