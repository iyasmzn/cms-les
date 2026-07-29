<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $programs = [
        [
            'title' => 'Kelas Grup Kecil',
            'excerpt' => 'Belajar dalam kelompok kecil agar setiap peserta tetap terpantau dan berkembang optimal.',
            'content' => '<p>Setiap kelompok dibatasi jumlah pesertanya sehingga pelatih dapat memberi perhatian lebih ke tiap orang. Suasana belajar tetap seru karena berkelompok, namun tetap efektif.</p><ul><li>Rasio pelatih–peserta ideal</li><li>Materi disesuaikan level kelompok</li><li>Peserta saling memotivasi</li></ul>',
            'icon' => '👥',
            'category' => 'Unggulan',
            'is_published' => true,
            'sort_order' => 1,
        ],
        [
            'title' => 'Kelas Privat (Privat 1-on-1)',
            'excerpt' => 'Sesi eksklusif satu pelatih satu peserta dengan materi dan jadwal yang benar-benar personal.',
            'content' => '<p>Untuk yang ingin kemajuan lebih cepat, kelas privat memberikan perhatian penuh dari pelatih. Materi, tempo, dan jadwal disusun sesuai kebutuhan dan target Anda.</p><ul><li>Fokus penuh pada satu peserta</li><li>Jadwal fleksibel sesuai kesepakatan</li><li>Progres dievaluasi tiap sesi</li></ul>',
            'icon' => '🧑‍🏫',
            'category' => 'Unggulan',
            'is_published' => true,
            'sort_order' => 2,
        ],
        [
            'title' => 'Trial Class Gratis',
            'excerpt' => 'Coba dulu satu sesi tanpa biaya untuk merasakan suasana belajar sebelum mendaftar.',
            'content' => '<p>Belum yakin? Ikuti trial class gratis untuk mengenal pelatih, metode, dan suasana kelompok. Setelah trial, Anda bisa memilih kelompok yang paling cocok.</p><ul><li>Satu sesi percobaan tanpa biaya</li><li>Konsultasi penempatan level</li><li>Tanpa komitmen</li></ul>',
            'icon' => '🎁',
            'category' => 'Layanan',
            'is_published' => true,
            'sort_order' => 3,
        ],
        [
            'title' => 'Pelatih Bersertifikat',
            'excerpt' => 'Diajar oleh pelatih berpengalaman dan bersertifikat di bidangnya masing-masing.',
            'content' => '<p>Seluruh pelatih kami telah berpengalaman dan tersertifikasi. Mereka tidak hanya mahir secara teknik, tetapi juga sabar dan komunikatif dalam mendampingi peserta dari berbagai usia.</p><ul><li>Pelatih tersertifikasi</li><li>Metode ramah pemula</li><li>Pendekatan personal</li></ul>',
            'icon' => '🏅',
            'category' => 'Fasilitas',
            'is_published' => true,
            'sort_order' => 4,
        ],
        [
            'title' => 'Jadwal Fleksibel',
            'excerpt' => 'Pilih hari dan jam kelompok yang paling pas dengan aktivitas Anda atau anak.',
            'content' => '<p>Tersedia banyak pilihan kelompok dengan hari dan jam berbeda, termasuk sore dan akhir pekan. Anda bebas memilih kelompok yang sesuai, dan bisa memantau jadwalnya di halaman My Courses.</p><ul><li>Pilihan hari & jam beragam</li><li>Kelas sore dan akhir pekan</li><li>Jadwal & kehadiran tercatat rapi</li></ul>',
            'icon' => '🗓️',
            'category' => 'Layanan',
            'is_published' => true,
            'sort_order' => 5,
        ],
        [
            'title' => 'Evaluasi & Sertifikat',
            'excerpt' => 'Perkembangan peserta dievaluasi berkala dan diapresiasi dengan sertifikat kelulusan level.',
            'content' => '<p>Kemajuan setiap peserta dipantau dan dilaporkan secara berkala. Saat peserta menuntaskan sebuah level, mereka memperoleh sertifikat sebagai bentuk apresiasi dan motivasi untuk melanjutkan ke level berikutnya.</p><ul><li>Laporan perkembangan berkala</li><li>Sertifikat tiap kenaikan level</li><li>Rekomendasi kelompok lanjutan</li></ul>',
            'icon' => '📜',
            'category' => 'Fasilitas',
            'is_published' => true,
            'sort_order' => 6,
        ],
    ];

    public function run(): void
    {
        foreach ($this->programs as $data) {
            Program::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
            );
        }
    }
}
