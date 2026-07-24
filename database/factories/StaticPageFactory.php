<?php

namespace Database\Factories;

use App\Models\StaticPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<StaticPage>
 */
class StaticPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'meta_description' => fake()->optional()->sentence(12),
            'content' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 99),
        ];
    }
}
