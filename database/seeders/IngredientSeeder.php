<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        $samples = [
            [
                'name' => 'Beras Pulen Cianjur',
                'category' => 'bahan_pokok',
                'stock' => 25.0,
                'unit' => 'kg',
                'min_stock' => 10.0,
                'cost_per_unit' => 14000,
                'supplier' => 'Toko Beras Barokah',
                'notes' => 'Bahan pokok nasi putih & lontong',
            ],
            [
                'name' => 'Daging Ayam Fillet',
                'category' => 'daging_telur',
                'stock' => 12.5,
                'unit' => 'kg',
                'min_stock' => 5.0,
                'cost_per_unit' => 38000,
                'supplier' => 'Agen Unggas Segar',
                'notes' => 'Untuk menu ayam bakar & geprek',
            ],
            [
                'name' => 'Minyak Goreng Sawit',
                'category' => 'bahan_pokok',
                'stock' => 8.0,
                'unit' => 'liter',
                'min_stock' => 5.0,
                'cost_per_unit' => 17000,
                'supplier' => 'Grosir Sembako Jaya',
                'notes' => 'Minyak kemasan pouch 2L',
            ],
            [
                'name' => 'Telur Ayam Negeri',
                'category' => 'daging_telur',
                'stock' => 45.0,
                'unit' => 'butir',
                'min_stock' => 20.0,
                'cost_per_unit' => 2000,
                'supplier' => 'Peternakan Berkah',
                'notes' => 'Untuk telur dadar, ceplok & orak arik',
            ],
            [
                'name' => 'Bawang Merah & Putih',
                'category' => 'bumbu_dapur',
                'stock' => 3.0,
                'unit' => 'kg',
                'min_stock' => 1.5,
                'cost_per_unit' => 32000,
                'supplier' => 'Pasar Tradisional',
                'notes' => 'Bumbu dasar aneka tumisan & sambal',
            ],
            [
                'name' => 'Cabai Rawit Merah',
                'category' => 'bumbu_dapur',
                'stock' => 1.2,
                'unit' => 'kg',
                'min_stock' => 1.5, // Stok menipis
                'cost_per_unit' => 45000,
                'supplier' => 'Pasar Tradisional',
                'notes' => 'Untuk sambal pedas geprek & penyet',
            ],
            [
                'name' => 'Teh Celup Melati Jumbo',
                'category' => 'minuman',
                'stock' => 4.0,
                'unit' => 'pack',
                'min_stock' => 2.0,
                'cost_per_unit' => 15000,
                'supplier' => 'Distributor Teh',
                'notes' => 'Bahan dasar es teh manis kantin',
            ],
            [
                'name' => 'Gula Pasir Kristal Putih',
                'category' => 'bahan_pokok',
                'stock' => 7.5,
                'unit' => 'kg',
                'min_stock' => 3.0,
                'cost_per_unit' => 17500,
                'supplier' => 'Grosir Sembako Jaya',
                'notes' => 'Pemanis minuman & bumbu masakan',
            ],
            [
                'name' => 'Cup Plastik Minuman 16oz',
                'category' => 'kemasan',
                'stock' => 120.0,
                'unit' => 'pcs',
                'min_stock' => 50.0,
                'cost_per_unit' => 450,
                'supplier' => 'Toko Plastik Makmur',
                'notes' => 'Cup es teh & aneka jus',
            ],
            [
                'name' => 'Kotak Nasi Kertas Kraft',
                'category' => 'kemasan',
                'stock' => 35.0,
                'unit' => 'pcs',
                'min_stock' => 40.0, // Stok menipis
                'cost_per_unit' => 1200,
                'supplier' => 'Toko Plastik Makmur',
                'notes' => 'Kemasan take-away ramah lingkungan',
            ],
            [
                'name' => 'Es Batu Kristal Higienis',
                'category' => 'minuman',
                'stock' => 0.0, // Sengaja stok habis (0) untuk pengujian
                'unit' => 'pack',
                'min_stock' => 3.0,
                'cost_per_unit' => 10000,
                'supplier' => 'Depot Es Kristal',
                'notes' => 'Perlu restock segera untuk jam istirahat!',
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($samples as $sample) {
                Ingredient::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'name' => $sample['name'],
                    ],
                    $sample
                );
            }
        }
    }
}
