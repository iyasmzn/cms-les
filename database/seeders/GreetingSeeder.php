<?php

namespace Database\Seeders;

use App\Models\Greeting;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class GreetingSeeder extends Seeder
{
    public function run(): void
    {
        if (Greeting::exists()) {
            return;
        }

        // Migrasi data lama dari pengaturan kepala sekolah (settings principal_*).
        $legacyName = Setting::get('principal_name');

        if ($legacyName) {
            Greeting::create([
                'name' => $legacyName,
                'position' => Setting::get('principal_title', 'Direktur'),
                'nip' => Setting::get('principal_nip'),
                'photo' => Setting::get('principal_photo'),
                'excerpt' => Setting::get('principal_excerpt'),
                'page_slug' => Setting::get('principal_page'),
                'is_published' => true,
                'sort_order' => 1,
            ]);

            return;
        }

        $greetings = [
            [
                'name' => 'Budi Santoso',
                'position' => 'Direktur',
                'nip' => null,
                'photo' => null,
                'excerpt' => "Selamat datang di website resmi kami. Dengan bangga kami menghadirkan platform digital yang dirancang untuk memudahkan Anda mengakses informasi, layanan, dan berbagai kegiatan kami.\n\nKami berkomitmen untuk terus berinovasi dan memberikan pelayanan terbaik. Bersama seluruh tim, kami berupaya menghadirkan pengalaman yang bermanfaat bagi setiap pengunjung, anggota, dan mitra kami.",
                'page_slug' => 'sambutan-direktur',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Dewi Lestari',
                'position' => 'Manajer Operasional',
                'nip' => null,
                'photo' => null,
                'excerpt' => "Terima kasih telah mengunjungi website kami. Melalui media ini, kami berharap dapat menjalin komunikasi yang baik dengan seluruh anggota, mitra, dan masyarakat luas.\n\nKami terus berupaya menghadirkan layanan yang responsif, transparan, dan modern demi kepuasan Anda.",
                'page_slug' => null,
                'is_published' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($greetings as $greeting) {
            Greeting::create($greeting);
        }
    }
}
