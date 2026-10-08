<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantAndProduct($user)
    {
        $tenant = Tenant::create([
            'user_id' => $user->id,
            'name' => 'Tenant Uji',
            'slug' => 'tenant-uji',
            'status' => 'active',
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Menu Uji',
            'slug' => 'menu-uji',
            'price' => 10000,
            'stock' => 0,
            'is_available' => false,
            'category' => 'makanan_berat',
        ]);

        return [$tenant, $product];
    }

    public function test_authenticated_user_can_view_dashboard_with_products()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->createTenantAndProduct($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Daftar Produk & Menu', false);
        $response->assertSee('Katalog Menu & Pengaturan Stok', false);
    }

    public function test_quick_update_changes_stock_and_price()
    {
        $user = User::factory()->create(['role' => 'admin']);
        [$tenant, $product] = $this->createTenantAndProduct($user);

        $response = $this->actingAs($user)->patch("/dashboard/products/{$product->id}/quick-update", [
            'stock' => 25,
            'price' => 17500,
        ]);

        $response->assertRedirect('/dashboard?tab=products');

        $product->refresh();
        $this->assertEquals(25, $product->stock);
        $this->assertEquals(17500, (int) $product->price);
        $this->assertTrue((bool) $product->is_available);
    }

    public function test_quick_update_ajax_returns_json()
    {
        $user = User::factory()->create(['role' => 'admin']);
        [$tenant, $product] = $this->createTenantAndProduct($user);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->patch("/dashboard/products/{$product->id}/quick-update", [
                'stock' => 10,
                'price' => 15000,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'stock' => 10,
                'price' => 15000,
            ]
        ]);
    }
}
