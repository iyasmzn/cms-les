<?php

namespace Database\Factories;

use App\Models\Greeting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Greeting>
 */
class GreetingFactory extends Factory
{
    /** @var array<string> */
    private static array $positions = [
        'Kepala Yayasan',
        'Kepala Sekolah',
        'Ketua Komite',
        'Wakil Kepala Sekolah',
        'Pembina Yayasan',
    ];

    /** @var array<string> */
    private static array $excerpts = [
        "Assalamu'alaikum Warahmatullahi Wabarakatuh. Puji syukur kepada Allah SWT atas segala nikmat dan karunia-Nya. Kami berkomitmen memberikan pendidikan terbaik untuk mencetak generasi yang beriman, berilmu, dan berdaya saing.",
        'Kami terus berupaya menghadirkan lingkungan belajar yang kondusif, modern, dan islami demi masa depan para santri yang gemilang di dunia dan akhirat.',
        'Pendidikan bukan hanya soal ilmu pengetahuan, tetapi juga pembentukan karakter dan akhlak mulia. Itulah komitmen kami bersama seluruh pendidik.',
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'position' => $this->faker->randomElement(self::$positions),
            'nip' => null,
            'photo' => null,
            'excerpt' => $this->faker->randomElement(self::$excerpts),
            'page_slug' => null,
            'is_published' => $this->faker->boolean(85),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
