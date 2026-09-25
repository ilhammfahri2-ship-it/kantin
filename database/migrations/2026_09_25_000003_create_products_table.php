<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel products (menu makanan/minuman per tenant).
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');                            // Nama menu, mis: "Nasi Ayam Penyet"
            $table->string('slug')->unique();                  // URL-friendly identifier
            $table->text('description')->nullable();           // Deskripsi menu
            $table->string('image')->nullable();               // Path gambar menu
            $table->decimal('price', 10, 2);                  // Harga (maks 99.999.999,99)
            $table->enum('category', [
                'makanan_berat',
                'makanan_ringan',
                'minuman',
                'dessert',
                'lainnya',
            ])->default('makanan_berat');
            $table->integer('stock')->default(0);              // Stok tersedia
            $table->boolean('is_available')->default(true);    // Toggle ketersediaan cepat
            $table->boolean('is_featured')->default(false);    // Tampil di hero/unggulan
            $table->timestamps();

            // Index untuk query katalog yang cepat
            $table->index(['tenant_id', 'is_available']);
            $table->index(['category', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
