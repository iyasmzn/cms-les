<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Seed the education units (jenjang) that each run their own PPDB.
     */
    public function run(): void
    {
        $institutions = [
            [
                'slug' => 'sd',
                'name' => 'Sekolah Dasar',
                'short_name' => 'SD',
                'icon' => '🎒',
                'color' => 'info',
                'description' => 'Jenjang pendidikan dasar untuk kelas 1 hingga 6.',
                'sort_order' => 1,
            ],
            [
                'slug' => 'smp',
                'name' => 'Sekolah Menengah Pertama',
                'short_name' => 'SMP',
                'icon' => '📘',
                'color' => 'success',
                'description' => 'Jenjang pendidikan menengah pertama untuk kelas 7 hingga 9.',
                'sort_order' => 2,
            ],
            [
                'slug' => 'sma',
                'name' => 'Sekolah Menengah Atas',
                'short_name' => 'SMA',
                'icon' => '🎓',
                'color' => 'warning',
                'description' => 'Jenjang pendidikan menengah atas untuk kelas 10 hingga 12.',
                'sort_order' => 3,
            ],
        ];

        foreach ($institutions as $institution) {
            Institution::updateOrCreate(
                ['slug' => $institution['slug']],
                array_merge($institution, ['is_active' => true]),
            );
        }
    }
}
