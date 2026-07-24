<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    /** @var array<string> */
    private array $categories = ['Berita', 'Akademik', 'Lingkungan', 'Event', 'Teknologi', 'Kesehatan', 'Prestasi'];

    /** @var array<array{name:string,initials:string}> */
    private array $authors = [
        ['name' => 'Ahmad Fauzi, M.Pd.', 'initials' => 'AF'],
        ['name' => 'Siti Rahayu, S.Pd.',  'initials' => 'SR'],
        ['name' => 'Budi Santoso',         'initials' => 'BS'],
        ['name' => 'Dewi Lestari',         'initials' => 'DL'],
    ];

    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        $author = $this->faker->randomElement($this->authors);
        $pubAt = $this->faker->dateTimeBetween('-6 months', 'now');

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->generateContent(),
            'image' => null,                           // null → picsum fallback
            'category' => $this->faker->randomElement($this->categories),
            'author' => $author['name'],
            'author_initials' => $author['initials'],
            'read_time' => $this->faker->numberBetween(2, 8),
            'is_published' => true,
            'published_at' => $pubAt,
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false, 'published_at' => null]);
    }

    private function generateContent(): string
    {
        $paragraphs = $this->faker->paragraphs($this->faker->numberBetween(4, 7));
        $html = '<p>'.implode('</p><p>', $paragraphs).'</p>';

        // inject an h2 heading after first paragraph
        $parts = explode('</p>', $html, 2);

        return $parts[0].'</p><h2>'.$this->faker->sentence(5).'</h2>'.$parts[1];
    }
}
