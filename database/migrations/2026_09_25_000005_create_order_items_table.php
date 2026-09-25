<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel order_items (detail item per pesanan).
     * Snapshot harga dan nama produk saat checkout untuk mencegah inkonsistensi data historis.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict'); // jangan hapus produk yang ada di order
            $table->string('product_name');         // Snapshot nama produk saat checkout
            $table->decimal('price', 10, 2);        // Snapshot harga saat checkout
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);     // price * quantity
            $table->text('notes')->nullable();       // Catatan per item, mis: "tidak pedas"
            $table->timestamps();

            // Index untuk query cepat detail order
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
