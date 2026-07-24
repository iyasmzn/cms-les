<?php

namespace Database\Factories;

use App\Models\Popup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Popup>
 */
class PopupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraph(),
            'image' => null,
            'button_label' => $this->faker->randomElement(['Selengkapnya', 'Daftar Sekarang', 'Lihat Detail', null]),
            'button_url' => $this->faker->optional()->url(),
            'open_in_new_tab' => false,
            'delay_seconds' => $this->faker->randomElement([0, 2, 3]),
            'show_every_days' => $this->faker->randomElement([0, 1, 7]),
            'width' => $this->faker->randomElement(['sm', 'md', 'lg']),
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
