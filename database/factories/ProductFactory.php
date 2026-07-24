<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->randomElement([
            'Kaos Polos Premium', 'Sepatu Sneakers Casual', 'Tas Ransel Laptop',
            'Botol Minum Stainless', 'Headset Bluetooth', 'Power Bank 10000mAh',
            'Jam Tangan Digital', 'Dompet Kulit Pria', 'Topi Baseball',
            'Mouse Wireless', 'Keyboard Mekanik', 'Lampu Meja LED',
            'Payung Lipat Otomatis', 'Gelas Keramik Set', 'Bantal Leher Travel',
        ]).' '.Str::upper(Str::random(3));

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(4),
            'sku' => 'SKU-'.$this->faker->unique()->numerify('#####'),
            'brand' => $this->faker->randomElement([
                'Aurora', 'NextGen', 'Prima', 'UrbanStyle', 'MaxPro',
                'EcoLine', 'Sentosa', 'Kanaya',
            ]),
            'category' => $this->faker->randomElement([
                'Elektronik', 'Fashion', 'Rumah Tangga', 'Olahraga',
                'Aksesoris', 'Perlengkapan Kantor',
            ]),
            'description' => $this->faker->paragraph(3),
            'cover_image' => null,
            'gallery' => null,
            'price' => $this->faker->randomElement([
                25000, 35000, 49000, 75000, 99000, 125000,
                159000, 199000, 249000, 350000, 499000,
            ]),
            'stock' => $this->faker->numberBetween(0, 80),
            'weight_gram' => $this->faker->numberBetween(100, 2000),
            'is_available' => true,
            'sort_order' => 0,
        ];
    }
}
