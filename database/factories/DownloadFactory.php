<?php

namespace Database\Factories;

use App\Models\Download;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Download>
 */
class DownloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    private const CATEGORIES = [
        'Formulir',
        'Surat Edaran',
        'Pengumuman',
        'Akademik',
        'Administrasi',
        'Kalender',
    ];

    public function definition(): array
    {
        $filename = $this->faker->words(3, true).'.pdf';

        return [
            'title' => $this->faker->sentence(4, true),
            'description' => $this->faker->optional()->sentence(10),
            'category' => $this->faker->randomElement(self::CATEGORIES),
            'file_path' => 'downloads/'.$this->faker->uuid().'.pdf',
            'original_filename' => $filename,
            'file_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(50_000, 5_000_000),
            'download_count' => $this->faker->numberBetween(0, 200),
            'sort_order' => $this->faker->numberBetween(0, 99),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
