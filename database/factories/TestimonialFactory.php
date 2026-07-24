<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /** @var array<string> */
    private static array $messages = [
        'Sekolah ini telah membentuk karakter dan kepribadian saya. Guru-guru yang berdedikasi selalu memberikan yang terbaik untuk kami.',
        'Kenangan indah di sekolah ini tidak akan pernah terlupakan. Terima kasih atas ilmu dan pengalaman berharga yang telah diberikan.',
        'Di sini saya belajar bukan hanya ilmu pengetahuan, tapi juga nilai-nilai kehidupan yang sangat berguna di masa depan.',
        'Lingkungan sekolah yang kondusif dan teman-teman yang solid membuat masa belajar saya menjadi pengalaman yang menyenangkan.',
        'Saya sangat bersyukur bisa bersekolah di sini. Banyak prestasi yang bisa saya raih berkat dukungan guru dan fasilitas yang ada.',
        'Pesan saya untuk adik-adik, manfaatkan waktu sekolah sebaik mungkin. Rajin belajar dan ikuti kegiatan ekstrakurikuler untuk mengembangkan bakat.',
        'Sekolah ini bukan hanya tempat belajar, tapi juga rumah kedua yang penuh dengan kenangan bermakna bersama sahabat dan guru.',
        'Terima kasih kepada semua guru yang sudah mendidik dengan sabar dan penuh dedikasi. Kalian adalah pahlawan tanpa tanda jasa.',
    ];

    public function definition(): array
    {
        $entryYear = $this->faker->numberBetween(2015, 2022);

        return [
            'name' => $this->faker->name(),
            'class_year' => (string) $entryYear,
            'graduation_year' => (string) ($entryYear + 3),
            'message' => $this->faker->randomElement(self::$messages),
            'photo' => null,
            'is_published' => $this->faker->boolean(85),
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
