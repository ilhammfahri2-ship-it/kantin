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

        {{-- Info Tenant --}}
        <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background-color: var(--bg-surface-2);">
            <span style="color: var(--text-muted);">Pesanan dari:</span>
            <span class="font-semibold ml-1" style="color: var(--brand);" x-text="tenantName"></span>
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
                        <p class="text-sm font-medium truncate" style="color: var(--text-primary);" x-text="item.name"></p>
                        <p class="text-xs mt-0.5" style="color: var(--text-muted);"
                           x-text="'Rp ' + item.price.toLocaleString('id-ID') + ' / porsi'"></p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button @click="decrease(id)"
                                class="w-7 h-7 rounded-lg border text-lg leading-none transition-colors duration-100 hover:opacity-70"
                                style="border-color: var(--border-default); color: var(--text-primary); background: var(--bg-surface-2);"
                                :aria-label="'Kurangi ' + item.name">−</button>
                        <span class="w-6 text-center text-sm font-semibold" style="color: var(--text-primary);"
                              x-text="item.quantity"></span>
                        <button @click="increase(id)"
                                class="w-7 h-7 rounded-lg border text-lg leading-none transition-colors duration-100 hover:opacity-70"
                                style="border-color: var(--border-default); color: var(--text-primary); background: var(--bg-surface-2);"
                                :aria-label="'Tambah ' + item.name">+</button>
                    </div>
                    <div class="flex flex-col items-end shrink-0 gap-1">
                        <span class="text-sm font-bold w-24 text-right shrink-0" style="color: var(--text-primary);"
                              x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
                        <button @click="removeItem(id)" 
                                class="text-xs flex items-center gap-1 text-red-500 hover:text-red-600 transition-colors"
                                aria-label="Batalkan Pesanan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Batalkan Pesanan</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Ringkasan --}}
        <div class="rounded-xl border p-4 mb-6" style="border-color: var(--border-default); background-color: var(--bg-surface);">
            <div class="flex justify-between text-sm mb-2" style="color: var(--text-secondary);">
                <span>Subtotal (<span x-text="totalItems"></span> item)</span>
                <span x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
            </div>
            <div class="border-t pt-3 mt-2 flex justify-between font-bold" style="border-color: var(--border-default);">
                <span style="color: var(--text-primary);">Total</span>
                <span style="color: var(--brand);" x-text="'Rp ' + total.toLocaleString('id-ID')"></span>
            </div>
        </div>

        {{-- Data Pembeli --}}
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="customer-name" class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">
                    Nama Pemesan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="customer-name"
                    x-model="customerName"
                    required
                    placeholder="Contoh: Budi Santoso"
                    class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none focus:ring-2 transition-colors duration-150"
                    style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
                >
            </div>
            <div>
                <label for="customer-class" class="block text-sm font-medium mb-1.5" style="color: var(--text-secondary);">
                    Kelas <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="customer-class"
                    x-model="customerClass"
                    required
                    placeholder="Contoh: 10 IPA 1"
                    class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none focus:ring-2 transition-colors duration-150"
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
                placeholder="Contoh: tidak pedas, porsi besar, dll."
                class="w-full px-3 py-2.5 text-sm rounded-lg border outline-none resize-none focus:ring-2 transition-colors duration-150"
                style="background-color: var(--bg-surface); color: var(--text-primary); border-color: var(--border-default);"
            ></textarea>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="mb-8">
            <label class="block text-sm font-medium mb-3" style="color: var(--text-secondary);">
                Metode Pembayaran <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors"
                       :class="paymentMethod === 'cash' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700'">
                    <input type="radio" x-model="paymentMethod" value="cash" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <div>
                        <span class="block text-sm font-bold" style="color: var(--text-primary);">Uang Tunai (Cash)</span>
                        <span class="block text-xs" style="color: var(--text-muted);">Bayar di kasir / kantin</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-gray-800 transition-colors"
                       :class="paymentMethod === 'qris' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700'">
                    <input type="radio" x-model="paymentMethod" value="qris" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <div>
                        <span class="block text-sm font-bold" style="color: var(--text-primary);">QRIS</span>
                        <span class="block text-xs" style="color: var(--text-muted);">Pembayaran Digital</span>
                    </div>
                </label>
            </div>
            
            {{-- Muncul foto QRIS jika dipilih --}}
            <div x-show="paymentMethod === 'qris'" x-transition class="mt-4">
                <div class="bg-white p-4 rounded-xl border flex flex-col items-center justify-center dark:bg-gray-800 dark:border-gray-700 shadow-sm">
                    <span class="font-bold mb-3 text-emerald-600 dark:text-emerald-400 text-sm">Scan QRIS di Bawah Ini</span>
                    <div class="p-3 bg-white rounded-xl flex justify-center items-center shadow-sm mb-3 border border-gray-100 mx-auto" style="width: 300px; max-width: 100%;">
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
            <button
                class="flex-2 py-2.5 px-6 btn-brand rounded-lg text-sm justify-center"
                @click="checkout()"
            >
                Pesan Sekarang
            </button>
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
            customerName: '',
            customerClass: '',
            paymentMethod: 'cash',

            get items() {
                return Object.entries(this.itemsMap);
            },
            get tenantName() {
                const first = Object.values(this.itemsMap)[0];
                return first ? first.tenantName : '';
            },
            get totalItems() {
                return Object.values(this.itemsMap).reduce((s, i) => s + i.quantity, 0);
            },
            get total() {
                return Object.values(this.itemsMap).reduce((s, i) => s + (i.price * i.quantity), 0);
            },

            init() {
                this.itemsMap = window.getCart ? window.getCart() : {};
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
                        tenant_id: tenantId,
                        notes: this.notes,
                        customer_name: this.customerName,
                        customer_class: this.customerClass,
                        payment_method: this.paymentMethod
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.itemsMap = {};
                        sessionStorage.removeItem('ekantin_cart');
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
