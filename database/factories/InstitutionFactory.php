<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['SD', 'SMP', 'SMA', 'MI', 'MTs', 'MA']).' '.fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'short_name' => fake()->randomElement(['SD', 'SMP', 'SMA']),
            'icon' => fake()->randomElement(['🏫', '🎓', '📚']),
            'color' => fake()->randomElement(['primary', 'info', 'success', 'warning']),
            'description' => fake()->optional()->sentence(),
            'address' => fake()->optional()->address(),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
            'form_mode' => Institution::FORM_MODE_INTERNAL,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function externalLink(?string $url = null): static
    {
        return $this->state([
            'form_mode' => Institution::FORM_MODE_EXTERNAL_LINK,
            'external_url' => $url ?? fake()->url(),
        ]);
    }

    public function embed(?string $url = null): static
    {
        return $this->state([
            'form_mode' => Institution::FORM_MODE_EMBED,
            'embed_url' => $url ?? fake()->url(),
        ]);
    }
}
