@extends('layouts.app')

@section('title', 'Keranjang Belanja')
@section('meta_description', 'Tinjau dan konfirmasi pesananmu sebelum checkout.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8" x-data="cartPage()">

    <h1 class="text-2xl font-bold tracking-tight mb-6" style="color: var(--text-primary);">
        Keranjang Belanja
    </h1>

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
                    <span class="text-sm font-bold w-20 text-right shrink-0" style="color: var(--text-primary);"
                          x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
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
<script>
    function cartPage() {
        return {
            itemsMap: {},
            notes: '',

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

            clearCart() {
                if (confirm('Yakin ingin mengosongkan keranjang?')) {
                    this.itemsMap = {};
                    sessionStorage.removeItem('ekantin_cart');
                    window.refreshCartBadge && window.refreshCartBadge();
                }
            },

            checkout() {
                // TODO: kirim data ke route checkout
                alert('Fitur checkout akan segera hadir! 🚀');
            },
        };
    }
</script>
@endpush
