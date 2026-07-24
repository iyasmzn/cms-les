<?php

namespace Database\Factories;

use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Story>
 */
class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Muhammad Fauzan', 'Aisyah Rahmawati', 'Abdullah Hasan',
            'Fatimah Zahra', 'Umar Farouq', 'Khadijah Nur',
            'Ali Akbar', 'Maryam Sholihah',
        ];
        $name = $this->faker->randomElement($names);
        $title = 'Kisah '.$name.' di Pesantren';
        $publishedAt = $this->faker->dateTimeBetween('-2 years', 'now');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->randomNumber(4),
            'author_name' => $name,
            'author_class' => $this->faker->randomElement(['Kelas 1 Aliyah', 'Kelas 2 Aliyah', 'Kelas 3 Aliyah', 'Kelas 1 Tsanawiyah', 'Alumni']),
            'author_year' => (string) $this->faker->numberBetween(2018, 2025),
            'author_photo' => null,
            'excerpt' => $this->faker->sentence(15),
            'content' => '<p>'.$this->faker->paragraphs(4, true).'</p>',
            'image' => null,
            'is_published' => true,
            'published_at' => $publishedAt,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
