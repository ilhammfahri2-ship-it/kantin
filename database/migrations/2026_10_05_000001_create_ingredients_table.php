<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('category')->default('bahan_pokok'); // bahan_pokok, daging_telur, bumbu_dapur, sayuran, minuman, kemasan, lainnya
            $table->decimal('stock', 10, 2)->default(0);
            $table->string('unit')->default('kg'); // kg, gram, liter, ml, butir, pcs, pack, ikat
            $table->decimal('min_stock', 10, 2)->default(5);
            $table->decimal('cost_per_unit', 12, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
