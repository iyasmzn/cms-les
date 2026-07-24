<?php

namespace Database\Seeders;

use App\Models\AdmissionPath;
use Illuminate\Database\Seeder;

class AdmissionPathSeeder extends Seeder
{
    public function run(): void
    {
        $paths = [
            ['slug' => 'reguler', 'name' => 'Reguler', 'icon' => '📝', 'color' => 'info', 'description' => 'Jalur pendaftaran umum yang terbuka untuk semua calon.', 'sort_order' => 1],
            ['slug' => 'prestasi', 'name' => 'Prestasi', 'icon' => '🏆', 'color' => 'success', 'description' => 'Berdasarkan prestasi atau pencapaian yang dimiliki calon.', 'sort_order' => 2],
            ['slug' => 'beasiswa', 'name' => 'Beasiswa', 'icon' => '💚', 'color' => 'warning', 'description' => 'Untuk calon yang membutuhkan keringanan biaya.', 'sort_order' => 3],
            ['slug' => 'undangan', 'name' => 'Undangan', 'icon' => '✉️', 'color' => 'gray', 'description' => 'Jalur khusus melalui undangan atau rekomendasi.', 'sort_order' => 4],
        ];

        foreach ($paths as $path) {
            AdmissionPath::updateOrCreate(
                ['slug' => $path['slug']],
                array_merge($path, ['is_active' => true]),
            );
        }
    }
}
