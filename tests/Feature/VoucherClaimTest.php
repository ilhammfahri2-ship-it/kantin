<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherClaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherClaimTest extends TestCase
{
    use RefreshDatabase;
    public function test_unauthenticated_user_cannot_claim_voucher(): void
    {
        $response = $this->postJson(route('voucher.claim'), ['code' => 'KANTINHEMAT']);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'require_login' => true,
                 ]);
    }

    public function test_authenticated_user_can_claim_active_voucher_once(): void
    {
        $user = User::factory()->create();
        $voucher = Voucher::firstOrCreate(
            ['code' => 'KANTINHEMAT'],
            [
                'name' => 'Promo Diskon 20%',
                'discount_percent' => 20,
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ]
        );
        $voucher->update(['expires_at' => now()->addMonth(), 'is_active' => true]);

        // First claim: Success
        $response1 = $this->actingAs($user)->postJson(route('voucher.claim'), ['code' => 'KANTINHEMAT']);
        $response1->assertStatus(200)
                  ->assertJson([
                      'success' => true,
                      'voucher' => [
                          'code' => 'KANTINHEMAT',
                          'discount_percent' => 20,
                      ]
                  ]);

        $this->assertDatabaseHas('voucher_claims', [
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'is_used' => false,
        ]);

        // Second claim: Rejected (Satu Akun Satu Kali)
        $response2 = $this->actingAs($user)->postJson(route('voucher.claim'), ['code' => 'KANTINHEMAT']);
        $response2->assertStatus(422)
                  ->assertJson([
                      'success' => false,
                      'already_claimed' => true,
                  ]);
    }

    public function test_expired_voucher_cannot_be_claimed(): void
    {
        $user = User::factory()->create();
        $voucher = Voucher::firstOrCreate(
            ['code' => 'EXPIREDVOUCHER'],
            [
                'name' => 'Expired Promo',
                'discount_percent' => 20,
                'expires_at' => now()->subDay(),
                'is_active' => true,
            ]
        );
        $voucher->update(['expires_at' => now()->subDay()]);

        $response = $this->actingAs($user)->postJson(route('voucher.claim'), ['code' => 'EXPIREDVOUCHER']);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'expired' => true,
                 ]);
    }

    public function test_checkout_applies_twenty_percent_discount_and_marks_claim_used(): void
    {
        $user = User::factory()->create();
        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = \App\Models\Tenant::create([
            'user_id' => $tenantUser->id,
            'name' => 'Kantin Test',
            'slug' => 'kantin-test',
            'status' => 'active',
        ]);
        $product = \App\Models\Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Paket Nasi Hemat',
            'slug' => 'paket-nasi-hemat',
            'price' => 50000,
            'stock' => 10,
            'category' => 'makanan_berat',
            'is_available' => true,
        ]);

        $voucher = Voucher::firstOrCreate(
            ['code' => 'KANTINHEMAT'],
            [
                'name' => 'Promo Diskon 20%',
                'discount_percent' => 20,
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ]
        );

        // 1. Claim voucher
        $this->actingAs($user)->postJson(route('voucher.claim'), ['code' => 'KANTINHEMAT'])
             ->assertStatus(200);

        // Mock canteen schedule to open
        \Illuminate\Support\Carbon::setTestNow('2026-10-06 09:45:00'); // Sesi istirahat 1 (09:30 - 10:00)

        // 2. Perform checkout
        $checkoutResponse = $this->actingAs($user)->postJson(route('checkout.store'), [
            'items' => [
                ['id' => $product->id, 'quantity' => 1],
            ],
            'tenant_id' => $tenant->id,
            'customer_name' => 'Budi Siswa',
            'customer_class' => '12 IPA',
            'payment_method' => 'cash',
            'voucher_code' => 'KANTINHEMAT',
        ]);

        $checkoutResponse->assertStatus(200)
                         ->assertJson(['success' => true]);

        // Subtotal = 50.000, Discount 20% = 10.000, Total = 40.000
        $order = \App\Models\Order::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(50000, (int)$order->subtotal);
        $this->assertEquals(10000, (int)$order->discount);
        $this->assertEquals(40000, (int)$order->total);
        $this->assertEquals('KANTINHEMAT', $order->voucher_code);

        // Voucher claim marked as used
        $claim = VoucherClaim::where('user_id', $user->id)->where('voucher_id', $voucher->id)->first();
        $this->assertTrue((bool)$claim->is_used);
        $this->assertEquals($order->id, $claim->order_id);

        // 3. User cannot claim again even after using
        $repeatResponse = $this->actingAs($user)->postJson(route('voucher.claim'), ['code' => 'KANTINHEMAT']);
        $repeatResponse->assertStatus(422)
                       ->assertJson([
                           'success' => false,
                           'already_claimed' => true,
                       ]);

        \Illuminate\Support\Carbon::setTestNow();
    }
}
