<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    /** @var array<string> */
    private static array $positions = [
        'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Guru Matematika',
        'Guru Bahasa Indonesia', 'Guru Bahasa Inggris', 'Guru Fisika',
        'Guru Kimia', 'Guru Biologi', 'Guru Sejarah', 'Guru Geografi',
        'Guru Ekonomi', 'Guru Sosiologi', 'Guru Pendidikan Agama Islam',
        'Guru Seni Budaya', 'Guru Penjasorkes', 'Guru TIK', 'Guru BK',
    ];

    /** @var array<string> */
    private static array $educations = [
        'S1 Pendidikan Matematika', 'S1 Pendidikan Bahasa Indonesia',
        'S2 Pendidikan Fisika', 'S1 Pendidikan Kimia', 'S2 Manajemen Pendidikan',
        'S1 Bimbingan Konseling', 'S1 Pendidikan Jasmani',
    ];

    public function definition(): array
    {
        $position = $this->faker->randomElement(self::$positions);

        $phone = $this->faker->boolean(70)
            ? '(0'.$this->faker->numerify('##').') '.$this->faker->numerify('####-####')
            : null;

        return [
            'name' => 'Drs. '.$this->faker->name('male').', M.Pd.',
            'nip' => $this->faker->numerify('19######0######1###'),
            'position' => $position,
            'subject' => $position,
            'education' => $this->faker->randomElement(self::$educations),
            'phone' => $phone,
            'email' => $this->faker->boolean(80) ? $this->faker->safeEmail() : null,
            'whatsapp' => $this->faker->boolean(60) ? '628'.$this->faker->numerify('#########') : null,
            'photo' => null,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }
}
