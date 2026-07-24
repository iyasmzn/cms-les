<?php

namespace Database\Factories;

use App\Models\AdmissionPath;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AdmissionPath>
 */
class AdmissionPathFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'icon' => fake()->randomElement(['🏡', '🏆', '💚', '🔄', '⭐']),
            'description' => fake()->sentence(),
            'color' => fake()->randomElement(['info', 'success', 'warning', 'gray', 'primary']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
