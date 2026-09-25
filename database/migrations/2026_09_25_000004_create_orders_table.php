<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel orders (header pesanan).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');        // Pemesan
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');      // Lapak yang dipesan
            $table->string('order_number')->unique();                                // Nomor pesanan unik, mis: ORD-20260925-0001
            $table->enum('status', [
                'pending',      // Menunggu konfirmasi tenant
                'confirmed',    // Dikonfirmasi tenant
                'preparing',    // Sedang dipersiapkan
                'ready',        // Siap diambil
                'completed',    // Selesai
                'cancelled',    // Dibatalkan
            ])->default('pending');
            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'refunded',
            ])->default('unpaid');
            $table->decimal('subtotal', 12, 2)->default(0);    // Sebelum diskon
            $table->decimal('discount', 12, 2)->default(0);    // Total diskon
            $table->decimal('total', 12, 2)->default(0);       // Yang harus dibayar
            $table->text('notes')->nullable();                  // Catatan dari pemesan
            $table->timestamp('completed_at')->nullable();      // Waktu pesanan selesai
            $table->timestamps();

            // Index untuk riwayat pesanan user & tenant
            $table->index(['user_id', 'status']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
