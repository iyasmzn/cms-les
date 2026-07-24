<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Haflah Akhirussanah',
            'Wisuda Tahfidz Al-Quran',
            'Pengajian Umum Bersama Kyai',
            'Seminar Pendidikan Islam',
            'Lomba Pidato Bahasa Arab',
            'Festival Seni Islami',
            'Pesantren Kilat Ramadhan',
            'Khataman Al-Quran 30 Juz',
            'Reuni Alumni Pesantren',
            'Peringatan Isra Mi\'raj',
            'Maulid Nabi Muhammad SAW',
            'Halal Bihalal Keluarga Besar',
        ];
        $title = $this->faker->randomElement($titles).' '.$this->faker->year();
        $startsAt = $this->faker->dateTimeBetween('-1 month', '+3 months');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->randomNumber(4),
            'excerpt' => $this->faker->sentence(15),
            'content' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
            'image' => null,
            'category' => $this->faker->randomElement(['Pendidikan', 'Keagamaan', 'Sosial', 'Budaya']),
            'location' => $this->faker->randomElement([
                'Aula Utama Pesantren',
                'Masjid Jami\' Al-Falah',
                'Lapangan Utama',
                'Gedung Serbaguna',
            ]),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+3 hours'),
            'is_published' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
