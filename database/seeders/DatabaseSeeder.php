<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data demo e-Kantin.
     */
    public function run(): void
    {
        // ── 1. Admin ──────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@ekantin.test'],
            [
                'name'     => 'Admin e-Kantin',
                'password' => bcrypt('password'),
                'role'     => 'admin',
                'phone'    => '081200000000',
            ]
        );

        // ── 2. Tenant Users + Tenants ─────────────────────────────────────────
        $tenantsData = [
            [
                'user'   => ['name' => 'Pak Berkah', 'email' => 'berkah@ekantin.test'],
                'tenant' => [
                    'name'        => 'Warung Berkah',
                    'slug'        => 'warung-berkah',
                    'description' => 'Spesialis nasi campur dan lauk-pauk rumahan.',
                    'location'    => 'Blok A No. 1',
                    'open_at'     => '07:00:00',
                    'close_at'    => '14:00:00',
                ],
                'products' => [
                    ['name' => 'Nasi Ayam Penyet',    'price' => 15000, 'category' => 'makanan_berat',  'stock' => 20, 'is_featured' => true,  'description' => 'Nasi putih dengan ayam penyet crispy, sambal terasi, dan lalapan segar.'],
                    ['name' => 'Nasi Tempe Orek',     'price' => 10000, 'category' => 'makanan_berat',  'stock' => 15, 'is_featured' => false, 'description' => 'Nasi putih dengan tempe orek manis pedas khas rumahan.'],
                    ['name' => 'Es Teh Manis',        'price' => 3000,  'category' => 'minuman',        'stock' => 50, 'is_featured' => false, 'description' => 'Teh hitam segar dengan es batu dan gula pilihan.'],
                    ['name' => 'Es Jeruk Peras',      'price' => 5000,  'category' => 'minuman',        'stock' => 30, 'is_featured' => false, 'description' => 'Jeruk segar diperas langsung, dingin menyegarkan.'],
                ],
            ],
            [
                'user'   => ['name' => 'Bu Sari', 'email' => 'sari@ekantin.test'],
                'tenant' => [
                    'name'        => 'Kedai Sari',
                    'slug'        => 'kedai-sari',
                    'description' => 'Minuman kekinian dan jajanan favorit anak sekolah.',
                    'location'    => 'Blok B No. 3',
                    'open_at'     => '07:30:00',
                    'close_at'    => '15:00:00',
                ],
                'products' => [
                    ['name' => 'Mie Ayam Bakso',      'price' => 12000, 'category' => 'makanan_berat',  'stock' => 0,  'is_featured' => false, 'description' => 'Mie kenyal dengan ayam suwir, bakso kenyal, dan kuah kaldu gurih.'],
                    ['name' => 'Risol Mayo',           'price' => 4000,  'category' => 'makanan_ringan', 'stock' => 25, 'is_featured' => true,  'description' => 'Risol renyah isi sayuran dan saus mayo creamy.'],
                    ['name' => 'Lumpia Goreng',        'price' => 3500,  'category' => 'makanan_ringan', 'stock' => 5,  'is_featured' => false, 'description' => 'Lumpia renyah isi rebung dan sayuran, cocok untuk camilan.'],
                    ['name' => 'Es Cokelat Susu',     'price' => 8000,  'category' => 'minuman',        'stock' => 20, 'is_featured' => true,  'description' => 'Minuman cokelat susu kaya rasa, cocok diminum dingin.'],
                    ['name' => 'Pudding Cokelat',     'price' => 6000,  'category' => 'dessert',        'stock' => 10, 'is_featured' => false, 'description' => 'Puding lembut rasa cokelat dengan saus vla vanilla.'],
                ],
            ],
            [
                'user'   => ['name' => 'Pak Jaya', 'email' => 'jaya@ekantin.test'],
                'tenant' => [
                    'name'        => 'Grill & Go',
                    'slug'        => 'grill-and-go',
                    'description' => 'Spesialis ayam dan ikan bakar dengan bumbu rempah.',
                    'location'    => 'Blok C No. 2',
                    'open_at'     => '10:00:00',
                    'close_at'    => '14:30:00',
                ],
                'products' => [
                    ['name' => 'Ayam Bakar Madu',     'price' => 18000, 'category' => 'makanan_berat',  'stock' => 10, 'is_featured' => true,  'description' => 'Ayam kampung dibakar dengan bumbu madu dan rempah pilihan.'],
                    ['name' => 'Ikan Lele Bakar',     'price' => 14000, 'category' => 'makanan_berat',  'stock' => 8,  'is_featured' => false, 'description' => 'Lele segar dibakar dengan bumbu kuning khas Jawa.'],
                    ['name' => 'Nasi Putih',          'price' => 3000,  'category' => 'makanan_berat',  'stock' => 100,'is_featured' => false, 'description' => 'Nasi putih pulen kualitas premium.'],
                    ['name' => 'Air Mineral 600ml',   'price' => 4000,  'category' => 'minuman',        'stock' => 40, 'is_featured' => false, 'description' => 'Air mineral segar kemasan 600ml.'],
                ],
            ],
        ];

        foreach ($tenantsData as $data) {
            // Buat atau ambil user tenant
            $tenantUser = User::firstOrCreate(
                ['email' => $data['user']['email']],
                [
                    'name'     => $data['user']['name'],
                    'password' => bcrypt('password'),
                    'role'     => 'tenant',
                ]
            );

            // Buat tenant
            $tenant = Tenant::firstOrCreate(
                ['slug' => $data['tenant']['slug']],
                array_merge($data['tenant'], ['user_id' => $tenantUser->id, 'status' => 'active'])
            );

            // Buat produk
            foreach ($data['products'] as $productData) {
                $slug = Str::slug($productData['name']) . '-' . $tenant->id;
                Product::firstOrCreate(
                    ['slug' => $slug],
                    array_merge($productData, [
                        'tenant_id'    => $tenant->id,
                        'slug'         => $slug,
                        'is_available' => true,
                    ])
                );
            }
        }

        // ── 3. Sample Customer ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'pelanggan@ekantin.test'],
            [
                'name'     => 'Budi Santoso',
                'password' => bcrypt('password'),
                'role'     => 'customer',
                'phone'    => '081298765432',
            ]
        );

        $this->command->info('✅ Seeder e-Kantin berhasil! Data demo sudah tersedia.');
        $this->command->line('   Admin  : admin@ekantin.test / password');
        $this->command->line('   Tenant : berkah@ekantin.test / password');
        $this->command->line('   Pelanggan: pelanggan@ekantin.test / password');
    }
}
