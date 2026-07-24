<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'title' => 'Kaos Polos Cotton Combed 30s',
                'sku' => 'FSH-KAOS-001',
                'brand' => 'UrbanStyle',
                'category' => 'Fashion',
                'description' => 'Kaos polos bahan cotton combed 30s yang adem dan nyaman dipakai sehari-hari. Tersedia berbagai warna dengan jahitan rapi dan tahan lama.',
                'price' => 65000,
                'stock' => 120,
                'weight_gram' => 200,
                'sort_order' => 1,
            ],
            [
                'title' => 'Sepatu Sneakers Casual Pria',
                'sku' => 'FSH-SNK-002',
                'brand' => 'MaxPro',
                'category' => 'Fashion',
                'description' => 'Sneakers casual dengan sol empuk anti selip, cocok untuk aktivitas harian maupun olahraga ringan. Desain modern yang mudah dipadukan.',
                'price' => 249000,
                'stock' => 45,
                'weight_gram' => 800,
                'sort_order' => 2,
            ],
            [
                'title' => 'Tas Ransel Laptop Anti Air',
                'sku' => 'ACC-BAG-003',
                'brand' => 'NextGen',
                'category' => 'Aksesoris',
                'description' => 'Tas ransel dengan kompartemen laptop 15 inci, bahan anti air, dan port USB. Ringan namun kuat untuk kebutuhan kerja dan kuliah.',
                'price' => 189000,
                'stock' => 60,
                'weight_gram' => 700,
                'sort_order' => 3,
            ],
            [
                'title' => 'Headset Bluetooth TWS',
                'sku' => 'ELK-TWS-004',
                'brand' => 'Aurora',
                'category' => 'Elektronik',
                'description' => 'Earbuds nirkabel dengan kualitas suara jernih, baterai tahan lama, dan koneksi Bluetooth 5.3 yang stabil. Dilengkapi casing pengisi daya.',
                'price' => 159000,
                'stock' => 90,
                'weight_gram' => 150,
                'sort_order' => 4,
            ],
            [
                'title' => 'Power Bank 10000mAh Fast Charging',
                'sku' => 'ELK-PWR-005',
                'brand' => 'MaxPro',
                'category' => 'Elektronik',
                'description' => 'Power bank kapasitas 10000mAh dengan dukungan fast charging dua arah. Bodi ringkas dengan indikator LED dan proteksi keamanan.',
                'price' => 135000,
                'stock' => 75,
                'weight_gram' => 250,
                'sort_order' => 5,
            ],
            [
                'title' => 'Botol Minum Stainless 750ml',
                'sku' => 'RTG-BTL-006',
                'brand' => 'EcoLine',
                'category' => 'Rumah Tangga',
                'description' => 'Botol minum stainless steel dengan teknologi vakum, menjaga suhu panas maupun dingin hingga 12 jam. Bebas BPA dan tidak mudah bocor.',
                'price' => 89000,
                'stock' => 110,
                'weight_gram' => 400,
                'sort_order' => 6,
            ],
            [
                'title' => 'Mouse Wireless Silent Click',
                'sku' => 'ELK-MOU-007',
                'brand' => 'NextGen',
                'category' => 'Elektronik',
                'description' => 'Mouse nirkabel dengan klik senyap dan sensor presisi 1600 DPI. Hemat baterai dan nyaman digenggam untuk penggunaan jangka panjang.',
                'price' => 75000,
                'stock' => 130,
                'weight_gram' => 120,
                'sort_order' => 7,
            ],
            [
                'title' => 'Lampu Meja LED Minimalis',
                'sku' => 'RTG-LMP-008',
                'brand' => 'Aurora',
                'category' => 'Rumah Tangga',
                'description' => 'Lampu meja LED dengan tiga tingkat kecerahan dan desain minimalis. Hemat energi, cocok untuk belajar, bekerja, maupun dekorasi kamar.',
                'price' => 119000,
                'stock' => 55,
                'weight_gram' => 500,
                'sort_order' => 8,
            ],
            [
                'title' => 'Matras Yoga Anti Slip',
                'sku' => 'OLR-YGA-009',
                'brand' => 'Prima',
                'category' => 'Olahraga',
                'description' => 'Matras yoga tebal 6mm dengan permukaan anti slip di kedua sisi. Ringan dan mudah digulung, dilengkapi tali pengikat untuk dibawa bepergian.',
                'price' => 99000,
                'stock' => 70,
                'weight_gram' => 900,
                'sort_order' => 9,
            ],
            [
                'title' => 'Dompet Kulit Pria Slim',
                'sku' => 'ACC-DMP-010',
                'brand' => 'Kanaya',
                'category' => 'Aksesoris',
                'description' => 'Dompet kulit sintetis premium dengan desain slim dan banyak slot kartu. Tampil elegan dan praktis dimasukkan ke saku.',
                'price' => 79000,
                'stock' => 95,
                'weight_gram' => 150,
                'sort_order' => 10,
            ],
            [
                'title' => 'Set Alat Tulis Kantor',
                'sku' => 'OFC-ATK-011',
                'brand' => 'Sentosa',
                'category' => 'Perlengkapan Kantor',
                'description' => 'Paket alat tulis lengkap berisi pulpen, pensil, penghapus, dan penggaris. Cocok untuk kebutuhan kantor, sekolah, dan hadiah.',
                'price' => 45000,
                'stock' => 150,
                'weight_gram' => 300,
                'sort_order' => 11,
            ],
            [
                'title' => 'Keyboard Mekanik RGB',
                'sku' => 'ELK-KBD-012',
                'brand' => 'MaxPro',
                'category' => 'Elektronik',
                'description' => 'Keyboard mekanik dengan lampu RGB dan switch responsif untuk pengalaman mengetik dan bermain game yang memuaskan. Bodi kokoh anti gores.',
                'price' => 350000,
                'stock' => 40,
                'weight_gram' => 1100,
                'sort_order' => 12,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['slug' => Str::slug($product['title'])],
                array_merge($product, ['slug' => Str::slug($product['title']), 'is_available' => true])
            );
        }
    }
}
