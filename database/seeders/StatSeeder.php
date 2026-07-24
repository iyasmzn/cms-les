<?php

namespace Database\Seeders;

use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['icon' => '🚀', 'label' => 'Berdiri Sejak',  'value' => '2015',  'sub' => 'Terus berkembang',      'sort_order' => 1],
            ['icon' => '👥', 'label' => 'Pengguna Aktif', 'value' => '12.500', 'sub' => 'Di seluruh Indonesia',  'sort_order' => 2],
            ['icon' => '🌐', 'label' => 'Website Dibuat',  'value' => '3.200+', 'sub' => 'Beragam kebutuhan',     'sort_order' => 3],
            ['icon' => '⭐', 'label' => 'Kepuasan',        'value' => '98%',    'sub' => 'Rating pengguna',       'sort_order' => 4],
        ];

        foreach ($defaults as $data) {
            Stat::firstOrCreate(['label' => $data['label']], $data);
        }
    }
}
