<?php

namespace Database\Factories;

use App\Models\Stat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stat>
 */
class StatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => $this->faker->randomElement(['🏫', '🎓', '👨‍🏫', '🏆', '📚', '🔬']),
            'label' => $this->faker->words(2, true),
            'value' => (string) $this->faker->numberBetween(10, 2000),
            'sub' => $this->faker->sentence(3),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
