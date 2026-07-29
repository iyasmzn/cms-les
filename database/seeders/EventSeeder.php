<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->events() as $data) {
            Event::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
            );
        }
    }

    /**
     * Agenda kegiatan les. Dates are relative to the seed time so the "upcoming"
     * agenda on the landing page always has content.
     *
     * @return array<int, array<string, mixed>>
     */
    private function events(): array
    {
        $at = fn (int $days, string $time): string => Carbon::now()->addDays($days)->format('Y-m-d')." {$time}";

        return [
            [
                'title' => 'Open House & Trial Class Akbar',
                'excerpt' => 'Kunjungi lokasi les kami, kenalan dengan pelatih, dan ikuti trial class gratis untuk semua course.',
                'content' => '<p>Acara terbuka bagi calon peserta dan orang tua. Anda bisa melihat langsung fasilitas, berkenalan dengan para pelatih, dan mencoba satu sesi gratis dari course pilihan: renang, musik, bahasa Inggris, matematika, atau menggambar.</p><p>Tersedia promo pendaftaran khusus selama acara berlangsung.</p>',
                'category' => 'Open House',
                'location' => 'Kampus Utama',
                'starts_at' => $at(7, '08:00:00'),
                'ends_at' => $at(7, '15:00:00'),
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Try Out Matematika Gratis',
                'excerpt' => 'Latihan soal dan pembahasan bersama tutor untuk mengukur kemampuan sebelum ujian sekolah.',
                'content' => '<p>Ikuti try out matematika gratis untuk mengukur kesiapanmu. Setelah mengerjakan soal, akan ada sesi pembahasan bersama tutor Bimbel Matematika kami.</p><p>Terbuka untuk umum, kuota terbatas. Hasil try out menjadi acuan penempatan kelompok.</p>',
                'category' => 'Try Out',
                'location' => 'Ruang Kelas B',
                'starts_at' => $at(10, '09:00:00'),
                'ends_at' => $at(10, '12:00:00'),
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Lomba Renang Antar Kelompok',
                'excerpt' => 'Kompetisi persahabatan antar kelompok Les Renang dengan berbagai kategori usia dan gaya.',
                'content' => '<p>Ajang unjuk kemampuan bagi peserta Les Renang. Lomba dibagi menjadi beberapa kategori usia dan gaya renang. Selain seru, kegiatan ini melatih mental bertanding dan kebersamaan antar kelompok.</p><p>Terbuka untuk peserta aktif. Tersedia medali dan sertifikat bagi para juara.</p>',
                'category' => 'Lomba',
                'location' => 'Kolam Renang Utama',
                'starts_at' => $at(14, '07:30:00'),
                'ends_at' => $at(14, '12:00:00'),
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Konser Mini Peserta Les Musik',
                'excerpt' => 'Pentas seni menampilkan peserta Les Musik memainkan piano, gitar, dan vokal di panggung.',
                'content' => '<p>Malam apresiasi bagi peserta Les Musik untuk tampil di depan orang tua dan teman-teman. Dari permainan piano, gitar, hingga vokal — setiap peserta mendapat kesempatan menunjukkan hasil belajarnya.</p><p>Terbuka untuk keluarga peserta. Ayo beri dukungan!</p>',
                'category' => 'Pentas Seni',
                'location' => 'Aula Serbaguna',
                'starts_at' => $at(21, '18:30:00'),
                'ends_at' => $at(21, '21:00:00'),
                'is_published' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'English Speaking Fun Day',
                'excerpt' => 'Hari penuh permainan dan aktivitas berbahasa Inggris untuk melatih percaya diri berbicara.',
                'content' => '<p>Kegiatan seru bagi peserta Les Bahasa Inggris untuk mempraktikkan speaking lewat games, storytelling, dan mini drama. Tujuannya menumbuhkan rasa percaya diri berbicara dalam bahasa Inggris tanpa takut salah.</p><p>Dipandu oleh para tutor dengan suasana santai dan menyenangkan.</p>',
                'category' => 'Kegiatan',
                'location' => 'Ruang Kelas A',
                'starts_at' => $at(30, '09:00:00'),
                'ends_at' => $at(30, '13:00:00'),
                'is_published' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Workshop Parenting: Mendampingi Anak Belajar',
                'excerpt' => 'Sesi berbagi untuk orang tua tentang cara mendampingi dan memotivasi anak selama mengikuti les.',
                'content' => '<p>Workshop khusus orang tua peserta. Bersama narasumber, kita membahas cara mendampingi anak belajar di rumah, menjaga motivasi, serta berkomunikasi efektif dengan pelatih untuk mendukung perkembangan anak.</p><p>Gratis untuk orang tua peserta aktif.</p>',
                'category' => 'Workshop',
                'location' => 'Aula Serbaguna',
                'starts_at' => $at(35, '09:00:00'),
                'ends_at' => $at(35, '12:00:00'),
                'is_published' => true,
                'sort_order' => 6,
            ],
            [
                'title' => 'Pameran Karya Les Menggambar',
                'excerpt' => 'Pameran hasil karya peserta Les Menggambar, dari sketsa hingga lukisan penuh warna.',
                'content' => '<p>Peserta Les Menggambar memamerkan karya terbaik mereka. Pameran ini menjadi ruang apresiasi sekaligus motivasi bagi para peserta untuk terus berkarya.</p><p>Terbuka untuk umum. Pengunjung dapat memberikan apresiasi langsung kepada para seniman muda.</p>',
                'category' => 'Pameran',
                'location' => 'Galeri Mini',
                'starts_at' => $at(45, '10:00:00'),
                'ends_at' => $at(46, '17:00:00'),
                'is_published' => true,
                'sort_order' => 7,
            ],
            [
                'title' => 'Wisuda & Pembagian Sertifikat Peserta',
                'excerpt' => 'Seremoni apresiasi bagi peserta yang menuntaskan level, lengkap dengan penyerahan sertifikat.',
                'content' => '<p>Acara puncak untuk merayakan pencapaian para peserta yang berhasil menyelesaikan level mereka. Sertifikat diserahkan sebagai bentuk apresiasi dan motivasi melanjutkan ke level berikutnya.</p><p>Dihadiri peserta, orang tua, dan seluruh pelatih.</p>',
                'category' => 'Wisuda',
                'location' => 'Aula Serbaguna',
                'starts_at' => $at(60, '09:00:00'),
                'ends_at' => $at(60, '12:00:00'),
                'is_published' => true,
                'sort_order' => 8,
            ],
        ];
    }
}
