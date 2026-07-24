<?php

namespace Database\Seeders;

use App\Models\Slide;
use Illuminate\Database\Seeder;

class SlideSeeder extends Seeder
{
    public function run(): void
    {
        if (Slide::exists()) {
            return;
        }

        $defaults = [
            [
                'title' => 'Selamat Datang di Demo CMS',
                'subtitle' => 'Platform serba guna untuk membangun dan mengelola website Anda dengan mudah, cepat, dan profesional.',
                'button_label' => 'Pelajari Lebih Lanjut',
                'button_url' => '#sambutan',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Kelola Konten Tanpa Ribet',
                'subtitle' => 'Buat artikel, atur halaman, dan kelola media hanya dengan beberapa klik — tanpa perlu keahlian teknis.',
                'button_label' => 'Lihat Fitur',
                'button_url' => '/program',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Pendaftaran 2026 Telah Dibuka',
                'subtitle' => 'Segera daftarkan diri Anda dan nikmati seluruh layanan yang tersedia. Isi formulir pendaftaran online sekarang.',
                'button_label' => 'Daftar Sekarang',
                'button_url' => '/ppdb',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($defaults as $data) {
            Slide::create($data);
        }
    }
}
