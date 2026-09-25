import './bootstrap';

/**
 * app.js — Titik masuk utama JavaScript e-Kantin
 *
 * Alpine.js di-import dari CDN di layout (untuk simplisitas & caching browser).
 * File ini digunakan untuk logika JavaScript tambahan yang tidak memerlukan Alpine.
 */

// ─── Cart: Fungsi global helper untuk keranjang ──────────────────────────────

/**
 * Ambil isi cart dari sessionStorage.
 * Format: { productId: { name, price, quantity, tenantId, tenantName } }
 */
window.getCart = function () {
    try {
        return JSON.parse(sessionStorage.getItem('ekantin_cart') || '{}');
    } catch {
        return {};
    }
};

/**
 * Hitung total item di cart (untuk badge navbar).
 */
window.getCartCount = function () {
    const cart = window.getCart();
    return Object.values(cart).reduce((sum, item) => sum + (item.quantity || 0), 0);
};

/**
 * Tambah produk ke cart.
 */
window.addToCart = function ({ productId, name, price, tenantId, tenantName }) {
    const cart = window.getCart();

    // Validasi: cart hanya boleh berisi produk dari 1 tenant
    const existingTenantIds = [...new Set(Object.values(cart).map(i => i.tenantId))];
    if (existingTenantIds.length > 0 && !existingTenantIds.includes(tenantId)) {
        return {
            success: false,
            message: `Keranjangmu sudah berisi pesanan dari ${tenantName !== undefined ? existingTenantIds[0] : 'tenant lain'}. Kosongkan keranjang terlebih dahulu?`,
        };
    }

    if (cart[productId]) {
        cart[productId].quantity += 1;
    } else {
        cart[productId] = { name, price, quantity: 1, tenantId, tenantName };
    }

    sessionStorage.setItem('ekantin_cart', JSON.stringify(cart));
    return { success: true };
};

/**
 * Dispatch event untuk update badge cart di navbar (Alpine reactivity).
 */
window.refreshCartBadge = function () {
    window.dispatchEvent(new CustomEvent('cart-updated', {
        detail: { count: window.getCartCount() },
    }));
};

// Refresh badge saat halaman pertama kali load
document.addEventListener('DOMContentLoaded', () => {
    window.refreshCartBadge();
});
