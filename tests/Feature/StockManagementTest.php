<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantUser()
    {
        $user = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id' => $user->id,
            'name' => 'Warung Uji',
            'slug' => 'warung-uji',
            'status' => 'active',
        ]);

        return [$user, $tenant];
    }

    public function test_authenticated_user_can_access_stock_management_page()
    {
        [$user, $tenant] = $this->createTenantUser();

        Ingredient::create([
            'tenant_id' => $tenant->id,
            'name' => 'Beras Cianjur',
            'category' => 'bahan_pokok',
            'stock' => 20.0,
            'unit' => 'kg',
            'min_stock' => 5.0,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/stok');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Stok Bahan & Produk', false);
        $response->assertSee('Beras Cianjur', false);
    }

    public function test_can_add_new_ingredient()
    {
        [$user, $tenant] = $this->createTenantUser();

        $response = $this->actingAs($user)->post('/dashboard/ingredients', [
            'name' => 'Minyak Kelapa',
            'category' => 'bahan_pokok',
            'stock' => 15.0,
            'unit' => 'liter',
            'min_stock' => 3.0,
            'cost_per_unit' => 20000,
            'supplier' => 'Pasar Tradisional',
        ]);

        $response->assertRedirect('/dashboard/stok?tab=ingredients');

        $this->assertDatabaseHas('ingredients', [
            'tenant_id' => $tenant->id,
            'name' => 'Minyak Kelapa',
            'stock' => 15.0,
            'unit' => 'liter',
        ]);
    }

    public function test_quick_adjust_restock_and_usage()
    {
        [$user, $tenant] = $this->createTenantUser();

        $ingredient = Ingredient::create([
            'tenant_id' => $tenant->id,
            'name' => 'Daging Sapi',
            'category' => 'daging_telur',
            'stock' => 10.0,
            'unit' => 'kg',
            'min_stock' => 4.0,
        ]);

        // 1. Restock +5 kg
        $response = $this->actingAs($user)->patch("/dashboard/ingredients/{$ingredient->id}/adjust", [
            'type' => 'in',
            'amount' => 5.0,
        ]);

        $response->assertRedirect('/dashboard/stok?tab=ingredients');
        $ingredient->refresh();
        $this->assertEquals(15.0, (float) $ingredient->stock);

        // 2. Pemakaian -3.5 kg
        $response = $this->actingAs($user)->patch("/dashboard/ingredients/{$ingredient->id}/adjust", [
            'type' => 'out',
            'amount' => 3.5,
        ]);

        $ingredient->refresh();
        $this->assertEquals(11.5, (float) $ingredient->stock);
    }

    public function test_quick_adjust_ajax_returns_json()
    {
        [$user, $tenant] = $this->createTenantUser();

        $ingredient = Ingredient::create([
            'tenant_id' => $tenant->id,
            'name' => 'Telur Ayam',
            'category' => 'daging_telur',
            'stock' => 20.0,
            'unit' => 'butir',
            'min_stock' => 10.0,
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->patch("/dashboard/ingredients/{$ingredient->id}/adjust", [
                'type' => 'in',
                'amount' => 30.0,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'ingredient' => [
                'id' => $ingredient->id,
                'stock' => 50.0,
            ]
        ]);
    }
}
