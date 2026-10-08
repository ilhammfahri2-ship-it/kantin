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
        // Tabel master voucher promo
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->integer('discount_percent')->default(20);
            $table->dateTime('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel pencatatan klaim voucher: 1 akun user_id hanya 1 kali klaim
        Schema::create('voucher_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('claimed_at')->useCurrent();
            $table->boolean('is_used')->default(false);
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->timestamps();

            // Constraint: 1 akun (user_id) hanya bisa mengklaim voucher_id 1 kali saja
            $table->unique(['voucher_id', 'user_id']);
        });

        // Tambah kolom voucher_code di tabel orders jika belum ada
        if (!Schema::hasColumn('orders', 'voucher_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('voucher_code')->nullable()->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_claims');
        Schema::dropIfExists('vouchers');

        if (Schema::hasColumn('orders', 'voucher_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('voucher_code');
            });
        }
    }
};
