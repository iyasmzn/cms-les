<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Program Tahfidz Al-Quran',
            'Madrasah Diniyah',
            'Program Terpadu',
            'Bahasa Arab Intensif',
            'Program Kitab Kuning',
        ];
        $title = $this->faker->randomElement($titles);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->randomNumber(4),
            'excerpt' => $this->faker->sentence(12),
            'content' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
            'image' => null,
            'icon' => $this->faker->randomElement(['academic-cap', 'book-open', 'star', 'heart', 'globe-alt']),
            'category' => $this->faker->randomElement(['Formal', 'Non-Formal', 'Unggulan']),
            'is_published' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
