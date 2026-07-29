<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Seed the courses (les) this site offers. Every institution here is a
     * course with groups (has_groups), not a formal school jenjang.
     */
    public function run(): void
    {
        $courses = [
            [
                'slug' => 'renang',
                'name' => 'Les Renang',
                'short_name' => 'RENANG',
                'icon' => '🏊',
                'color' => 'info',
                'description' => 'Belajar berenang untuk segala usia — dari pengenalan air hingga teknik gaya lengkap, dalam kelompok sesuai tingkat.',
                'sort_order' => 1,
            ],
            [
                'slug' => 'bahasa-inggris',
                'name' => 'Les Bahasa Inggris',
                'short_name' => 'ENGLISH',
                'icon' => '🗣️',
                'color' => 'success',
                'description' => 'Kelas percakapan & grammar dengan kelompok kecil, dari level pemula sampai mahir.',
                'sort_order' => 2,
            ],
            [
                'slug' => 'musik',
                'name' => 'Les Musik',
                'short_name' => 'MUSIK',
                'icon' => '🎹',
                'color' => 'warning',
                'description' => 'Piano, gitar, dan vokal bersama pelatih berpengalaman. Kelompok kecil agar tiap peserta terpantau.',
                'sort_order' => 3,
            ],
            [
                'slug' => 'matematika',
                'name' => 'Bimbel Matematika',
                'short_name' => 'MTK',
                'icon' => '➗',
                'color' => 'primary',
                'description' => 'Bimbingan belajar matematika terstruktur untuk menguatkan konsep dan persiapan ujian.',
                'sort_order' => 4,
            ],
            [
                'slug' => 'menggambar',
                'name' => 'Les Menggambar',
                'short_name' => 'GAMBAR',
                'icon' => '🎨',
                'color' => 'danger',
                'description' => 'Kelas menggambar & melukis untuk mengasah kreativitas anak dan remaja.',
                'sort_order' => 5,
            ],
        ];

        foreach ($courses as $course) {
            Institution::updateOrCreate(
                ['slug' => $course['slug']],
                array_merge($course, [
                    'is_active' => true,
                    'has_groups' => true,
                ]),
            );
        }
    }
}
