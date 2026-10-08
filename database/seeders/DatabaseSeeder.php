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
            ['email' => 'admin123@gmail.com'],
            [
                'name'      => 'Admin KantinSchool',
                'password'  => bcrypt('password'),
                'role'      => 'admin',
                'phone'     => '081200000000',
                'classroom' => 'Admin Sekolah',
            ]
        );

        // ── 2. Tenant Users + Tenants ─────────────────────────────────────────
        $tenantsData = [
            [
                'user'   => ['name' => 'Pak Berkah', 'email' => 'berkah@ekantin.test'],
                'tenant' => [
                    'name'        => 'Warung Berkah',
                    'slug'        => 'warung-berkah',
                    'description' => 'Spesialis nasi campur, ayam penyet krispi, dan masakan khas rumahan.',
                    'location'    => 'Blok A No. 1',
                    'open_at'     => '07:00:00',
                    'close_at'    => '14:00:00',
                ],
                'products' => [
                    ['name' => 'Nasi Ayam Penyet',          'price' => 15000, 'category' => 'makanan_berat',  'stock' => 20, 'is_featured' => true,  'image' => 'images/products/nasi-ayam-penyet.jpg',      'description' => 'Nasi putih dengan ayam penyet crispy, sambal terasi pedas, dan lalapan segar.'],
                    ['name' => 'Nasi Tempe Orek',           'price' => 10000, 'category' => 'makanan_berat',  'stock' => 15, 'is_featured' => false, 'image' => 'images/products/nasi-tempe-orek.jpg',       'description' => 'Nasi putih dengan tempe orek manis pedas gurih khas rumahan.'],
                    ['name' => 'Ayam Geprek Sambal Bawang',  'price' => 16000, 'category' => 'makanan_berat',  'stock' => 25, 'is_featured' => true,  'image' => 'images/products/ayam-geprek.jpg',           'description' => 'Ayam goreng krispi digeprek dengan sambal bawang cabai rawit pedas nampol.'],
                    ['name' => 'Nasi Rendang Sapi Padang',  'price' => 22000, 'category' => 'makanan_berat',  'stock' => 15, 'is_featured' => true,  'image' => 'images/products/nasi-rendang-padang.jpg',   'description' => 'Nasi putih hangat dengan rendang daging sapi empuk kaya bumbu rempah Minang.'],
                    ['name' => 'Soto Ayam Lamongan',        'price' => 14000, 'category' => 'makanan_berat',  'stock' => 18, 'is_featured' => false, 'image' => 'images/products/soto-ayam-lamongan.jpg',   'description' => 'Soto ayam kuah kaldu kuning gurih disajikan dengan bubuk koya dan soun.'],
                    ['name' => 'Sayur Asem Segar',          'price' => 6000,  'category' => 'makanan_berat',  'stock' => 20, 'is_featured' => false, 'image' => 'images/products/sayur-asem.jpg',           'description' => 'Sayur asem kuah bening segar dengan jagung manis, labu siam, dan melinjo.'],
                    ['name' => 'Tahu & Tempe Goreng',       'price' => 5000,  'category' => 'makanan_ringan', 'stock' => 30, 'is_featured' => false, 'image' => 'images/products/tahu-tempe-goreng.jpg',    'description' => 'Tahu dan tempe goreng bumbu ketumbar gurih garing disajikan hangat.'],
                    ['name' => 'Es Teh Manis',              'price' => 3000,  'category' => 'minuman',        'stock' => 50, 'is_featured' => false, 'image' => 'images/products/es-teh-manis.jpg',          'description' => 'Teh hitam segar diseduh melati dengan es batu kristal dan gula alami.'],
                    ['name' => 'Es Jeruk Peras',            'price' => 5000,  'category' => 'minuman',        'stock' => 30, 'is_featured' => false, 'image' => 'images/products/es-jeruk-peras.jpg',        'description' => 'Jeruk manis segar diperas langsung, dingin menyegarkan tenggorokan.'],
                    ['name' => 'Es Campur Spesial',         'price' => 9000,  'category' => 'minuman',        'stock' => 20, 'is_featured' => true,  'image' => 'images/products/es-campur.jpg',             'description' => 'Es serut dengan aneka cincau, nata de coco, nangka manis, dan sirup cocopandan.'],
                ],
            ],
            [
                'user'   => ['name' => 'Bu Sari', 'email' => 'sari@ekantin.test'],
                'tenant' => [
                    'name'        => 'Kedai Sari',
                    'slug'        => 'kedai-sari',
                    'description' => 'Minuman kekinian, mie lezat, dan aneka jajanan favorit anak sekolah.',
                    'location'    => 'Blok B No. 3',
                    'open_at'     => '07:30:00',
                    'close_at'    => '15:00:00',
                ],
                'products' => [
                    ['name' => 'Mie Ayam Bakso',            'price' => 12000, 'category' => 'makanan_berat',  'stock' => 20, 'is_featured' => true,  'image' => 'images/products/mie-ayam-bakso.jpg',        'description' => 'Mie kenyal dengan potongan ayam semur gurih, bakso sapi, dan pangsit renyah.'],
                    ['name' => 'Siomay Bandung',            'price' => 10000, 'category' => 'makanan_ringan', 'stock' => 25, 'is_featured' => true,  'image' => 'images/products/siomay-bandung.jpg',        'description' => 'Siomay ikan tenggiri kukus kenyal disiram bumbu kacang kental dan kecap manis.'],
                    ['name' => 'Dimsum Mentai',             'price' => 13000, 'category' => 'makanan_ringan', 'stock' => 20, 'is_featured' => true,  'image' => 'images/products/dimsum-mentai.jpg',         'description' => 'Dimsum ayam lembut dengan lelehan saus mentai gurih creamy yang di-torch.'],
                    ['name' => 'Risol Mayo',                 'price' => 4000,  'category' => 'makanan_ringan', 'stock' => 35, 'is_featured' => true,  'image' => 'images/products/risol-mayo.jpg',            'description' => 'Risol renyah garing berbalut tepung roti isi smoked beef, telur, dan mayo.'],
                    ['name' => 'Lumpia Goreng',              'price' => 3500,  'category' => 'makanan_ringan', 'stock' => 25, 'is_featured' => false, 'image' => 'images/products/lumpia-goreng.jpg',         'description' => 'Lumpia renyah isi rebung dan sayuran renyah dengan cocolan saus tauco.'],
                    ['name' => 'Cireng Rujak Crispy',        'price' => 5000,  'category' => 'makanan_ringan', 'stock' => 30, 'is_featured' => false, 'image' => 'images/products/cireng-rujak.jpg',         'description' => 'Cireng kenyal gurih renyah di luar disajikan dengan saus rujak manis pedas.'],
                    ['name' => 'Kentang Goreng French Fries','price' => 8000,  'category' => 'makanan_ringan', 'stock' => 25, 'is_featured' => false, 'image' => 'images/products/kentang-goreng.jpg',       'description' => 'Kentang goreng gurih garing keemasan dengan taburan bumbu barbeque favorit.'],
                    ['name' => 'Es Cokelat Susu',           'price' => 8000,  'category' => 'minuman',        'stock' => 25, 'is_featured' => true,  'image' => 'images/products/es-cokelat-susu.jpg',       'description' => 'Minuman cokelat susu premium pekat dan manis creamy disajikan dingin segar.'],
                    ['name' => 'Thai Tea Original',         'price' => 7000,  'category' => 'minuman',        'stock' => 30, 'is_featured' => false, 'image' => 'images/products/thai-tea.jpg',              'description' => 'Teh susu khas Thailand aromatik dengan rasa manis legit dan aroma teh kuat.'],
                    ['name' => 'Matcha Green Tea Latte',    'price' => 10000, 'category' => 'minuman',        'stock' => 20, 'is_featured' => false, 'image' => 'images/products/matcha-latte.jpg',         'description' => 'Matcha hijau jepang aromatik dipadukan dengan susu segar creamy dingin.'],
                    ['name' => 'Pudding Cokelat Vla',       'price' => 6000,  'category' => 'dessert',        'stock' => 15, 'is_featured' => false, 'image' => 'images/products/pudding-cokelat.jpg',       'description' => 'Puding sutra rasa cokelat manis dipadu dengan siraman saus vla vanila lembut.'],
                ],
            ],
            [
                'user'   => ['name' => 'Pak Jaya', 'email' => 'jaya@ekantin.test'],
                'tenant' => [
                    'name'        => 'Grill & Go',
                    'slug'        => 'grill-and-go',
                    'description' => 'Spesialis hidangan bakar madu, lauk panggangan rempah, dan dessert modern.',
                    'location'    => 'Blok C No. 2',
                    'open_at'     => '10:00:00',
                    'close_at'    => '14:30:00',
                ],
                'products' => [
                    ['name' => 'Ayam Bakar Madu',           'price' => 18000, 'category' => 'makanan_berat',  'stock' => 15, 'is_featured' => true,  'image' => 'images/products/ayam-bakar-madu.jpg',       'description' => 'Ayam bakar dengan olesan madu murni dan bumbu rempah bakar karamel legit.'],
                    ['name' => 'Bebek Goreng Sambal Ijo',   'price' => 24000, 'category' => 'makanan_berat',  'stock' => 12, 'is_featured' => true,  'image' => 'images/products/bebek-goreng.jpg',          'description' => 'Bebek empuk bumbu ungkep gurih digoreng garing dengan sambal cabai hijau.'],
                    ['name' => 'Ikan Lele Bakar',           'price' => 14000, 'category' => 'makanan_berat',  'stock' => 10, 'is_featured' => false, 'image' => 'images/products/ikan-lele-bakar.jpg',       'description' => 'Lele segar dibakar dengan bumbu kuning kecap manis dan lalapan segar.'],
                    ['name' => 'Sate Ayam Madura',          'price' => 16000, 'category' => 'makanan_berat',  'stock' => 20, 'is_featured' => true,  'image' => 'images/products/sate-ayam-madura.jpg',       'description' => 'Sate daging ayam bakar arang empuk dengan siraman bumbu kacang gurih manis.'],
                    ['name' => 'Nasi Gudeg Komplit',        'price' => 18000, 'category' => 'makanan_berat',  'stock' => 12, 'is_featured' => false, 'image' => 'images/products/nasi-gudeg.jpg',            'description' => 'Nasi gudeg nangka khas Jogja lengkap dengan krecek pedas dan telur pindang.'],
                    ['name' => 'Nasi Putih Pulen',          'price' => 3000,  'category' => 'makanan_berat',  'stock' => 100,'is_featured' => false, 'image' => 'images/products/nasi-putih.jpg',            'description' => 'Nasi putih beras pandan wangi pulen hangat kualitas prima.'],
                    ['name' => 'Es Kopi Susu Gula Aren',    'price' => 8000,  'category' => 'minuman',        'stock' => 35, 'is_featured' => true,  'image' => 'images/products/es-kopi-gula-aren.jpg',     'description' => 'Kopi espresso mantap berpadu susu segar creamy dan manis legit gula aren.'],
                    ['name' => 'Jus Alpukat Kocok',         'price' => 10000, 'category' => 'minuman',        'stock' => 20, 'is_featured' => false, 'image' => 'images/products/jus-alpukat.jpg',          'description' => 'Alpukat mentega matang dikocok kental dengan sirup cokelat manis creamy.'],
                    ['name' => 'Lemonade Mint Segar',       'price' => 7000,  'category' => 'minuman',        'stock' => 30, 'is_featured' => false, 'image' => 'images/products/lemonade-mint.jpg',        'description' => 'Perasan buah lemon segar asam manis dingin dengan aroma daun mint penyejuk.'],
                    ['name' => 'Croffle Caramel Butter',    'price' => 12000, 'category' => 'dessert',        'stock' => 15, 'is_featured' => true,  'image' => 'images/products/croffle.jpg',               'description' => 'Croissant wafel renyah mentega wangi bertabur gula karamel crispy.'],
                    ['name' => 'Mille Crepes Red Velvet',   'price' => 15000, 'category' => 'dessert',        'stock' => 10, 'is_featured' => false, 'image' => 'images/products/mille-crepes.jpg',          'description' => 'Kue lapisan crepes red velvet bertingkat dengan krim keju manis lumer di mulut.'],
                    ['name' => 'Waffle Ice Cream Vanilla',  'price' => 13000, 'category' => 'dessert',        'stock' => 12, 'is_featured' => false, 'image' => 'images/products/waffle-ice-cream.jpg',     'description' => 'Wafel hangat renyah disajikan dengan satu scoop es krim vanila dan saus cokelat.'],
                    ['name' => 'Air Mineral 600ml',         'price' => 4000,  'category' => 'minuman',        'stock' => 50, 'is_featured' => false, 'image' => 'images/products/air-mineral.jpg',           'description' => 'Air mineral pegunungan alami kemasan botol 600ml dingin higienis.'],
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
                Product::updateOrCreate(
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
                'name'      => 'Budi Santoso',
                'password'  => bcrypt('password'),
                'role'      => 'customer',
                'phone'     => '081298765432',
                'classroom' => '12 MIPA 1',
            ]
        );

        // ── 4. Voucher Promo KANTINHEMAT ─────────────────────────────────────
        \App\Models\Voucher::firstOrCreate(
            ['code' => 'KANTINHEMAT'],
            [
                'name'             => 'Promo Diskon Kantin 20%',
                'discount_percent' => 20,
                'expires_at'       => now()->addMonths(3)->endOfDay(), // Aktif 3 bulan ke depan
                'is_active'        => true,
            ]
        );

        $this->command->info('✅ Seeder e-Kantin berhasil! Data demo sudah tersedia.');
        $this->command->line('   Admin  : admin123@gmail.com / password');
        $this->command->line('   Tenant : berkah@ekantin.test / password');
        $this->command->line('   Pelanggan: pelanggan@ekantin.test / password');
        $this->command->line('   Voucher  : KANTINHEMAT (Diskon 20%)');
    }
}
