<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel tenants (lapak/booth kantin).
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // pemilik tenant
            $table->string('name');                        // Nama lapak, mis: "Warung Berkah"
            $table->string('slug')->unique();              // URL-friendly identifier
            $table->text('description')->nullable();       // Deskripsi singkat
            $table->string('logo')->nullable();            // Path gambar logo
            $table->string('banner')->nullable();          // Path gambar banner
            $table->string('location')->nullable();        // Lokasi fisik di kantin, mis: "Blok A No. 3"
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->time('open_at')->nullable();           // Jam buka, mis: 07:00
            $table->time('close_at')->nullable();          // Jam tutup, mis: 14:00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
