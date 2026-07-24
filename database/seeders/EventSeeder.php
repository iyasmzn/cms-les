<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $events = [
        [
            'title' => 'Temu Anggota Tahunan 2026',
            'excerpt' => 'Pertemuan tahunan seluruh anggota yang menampilkan diskusi panel, apresiasi, dan ramah tamah bersama.',
            'content' => '<p>Temu Anggota Tahunan merupakan agenda rutin sebagai ajang silaturahmi dan berbagi. Acara ini menampilkan diskusi panel, sesi berbagi pengalaman, serta pemberian apresiasi bagi anggota teraktif.</p><p>Seluruh anggota dan tamu undangan diundang untuk hadir. Acara berlangsung selama satu hari penuh, dimulai dengan sesi pembukaan dan diakhiri dengan ramah tamah bersama.</p>',
            'category' => 'Pertemuan',
            'location' => 'Gedung Serbaguna',
            'starts_at' => '2026-06-15 08:00:00',
            'ends_at' => '2026-06-15 17:00:00',
            'is_published' => true,
            'sort_order' => 1,
        ],
        [
            'title' => 'Workshop Pengelolaan Website',
            'excerpt' => 'Pelatihan praktis mengelola konten website: menulis artikel, mengatur menu, dan mengoptimalkan halaman.',
            'content' => '<p>Workshop ini dirancang bagi Anda yang ingin belajar mengelola website secara mandiri. Peserta akan dipandu langkah demi langkah mulai dari menulis artikel, mengatur menu navigasi, hingga mengoptimalkan halaman agar mudah ditemukan.</p><p>Terbuka untuk umum, baik pemula maupun yang sudah berpengalaman. Peserta akan mendapatkan materi dan sertifikat kehadiran.</p>',
            'category' => 'Workshop',
            'location' => 'Ruang Pelatihan',
            'starts_at' => '2026-07-03 09:00:00',
            'ends_at' => '2026-07-03 15:00:00',
            'is_published' => true,
            'sort_order' => 2,
        ],
        [
            'title' => 'Webinar: Strategi Konten Digital',
            'excerpt' => 'Diskusi daring bersama praktisi mengenai strategi membuat konten yang menarik dan konsisten.',
            'content' => '<p>Webinar rutin hadir kembali dengan menghadirkan praktisi di bidang konten digital. Pada sesi ini akan dibahas cara menyusun strategi konten yang menarik, konsisten, dan sesuai dengan audiens.</p><p>Terbuka untuk seluruh peserta secara daring. Tautan akses akan dikirimkan setelah pendaftaran.</p>',
            'category' => 'Webinar',
            'location' => 'Online (Zoom)',
            'starts_at' => '2026-06-06 19:00:00',
            'ends_at' => '2026-06-06 21:00:00',
            'is_published' => true,
            'sort_order' => 3,
        ],
        [
            'title' => 'Seminar Nasional: Transformasi Digital',
            'excerpt' => 'Seminar nasional membahas peluang dan tantangan transformasi digital bagi organisasi masa kini.',
            'content' => '<p>Transformasi digital membawa peluang sekaligus tantangan bagi setiap organisasi. Seminar ini menghadirkan pembicara dari berbagai kalangan — akademisi, praktisi teknologi, dan pelaku usaha — untuk berdiskusi mengenai strategi terbaik dalam beradaptasi dengan era digital.</p><p>Peserta akan mendapatkan sertifikat nasional dan materi seminar dalam bentuk e-book.</p>',
            'category' => 'Seminar',
            'location' => 'Aula Utama',
            'starts_at' => '2026-08-20 08:00:00',
            'ends_at' => '2026-08-20 16:00:00',
            'is_published' => true,
            'sort_order' => 4,
        ],
        [
            'title' => 'Lomba Menulis Artikel Tingkat Nasional',
            'excerpt' => 'Kompetisi menulis artikel antar peserta se-Indonesia memperebutkan hadiah total puluhan juta rupiah.',
            'content' => '<p>Kami kembali menyelenggarakan lomba menulis artikel tingkat nasional. Kompetisi ini terbuka untuk peserta dari seluruh Indonesia, dibagi menjadi kategori pelajar dan umum.</p><p>Hadiah utama berupa uang pembinaan, sertifikat, dan kesempatan artikel dimuat di media partner. Ayo tunjukkan karya terbaikmu!</p>',
            'category' => 'Lomba',
            'location' => 'Online',
            'starts_at' => '2026-09-14 08:00:00',
            'ends_at' => '2026-09-30 23:59:00',
            'is_published' => true,
            'sort_order' => 5,
        ],
        [
            'title' => 'Kelas Intensif Desain Grafis',
            'excerpt' => 'Program intensif 10 hari belajar desain grafis dari dasar hingga siap membuat materi promosi.',
            'content' => '<p>Kelas Intensif Desain Grafis hadir untuk Anda yang ingin menguasai keterampilan desain dari nol. Kegiatan berlangsung selama 10 hari dengan materi padat: dasar desain, tipografi, komposisi warna, hingga praktik membuat materi promosi siap pakai.</p><p>Terbuka untuk peserta umum. Kuota terbatas 30 peserta per kelas.</p>',
            'category' => 'Workshop',
            'location' => 'Lab Komputer',
            'starts_at' => '2026-03-15 09:00:00',
            'ends_at' => '2026-03-25 15:00:00',
            'is_published' => true,
            'sort_order' => 6,
        ],
        [
            'title' => 'Peringatan HUT ke-10',
            'excerpt' => 'Perayaan ulang tahun ke-10 bersama seluruh anggota, mitra, dan masyarakat sekitar.',
            'content' => '<p>Kami mengadakan peringatan Hari Ulang Tahun ke-10 yang terbuka untuk seluruh anggota dan masyarakat. Rangkaian acara meliputi seremonial, hiburan, bazar, dan doa bersama.</p><p>Acara ini juga dimeriahkan oleh penampilan dari komunitas mitra dari berbagai daerah.</p>',
            'category' => 'Peringatan Hari Besar',
            'location' => 'Lapangan Utama',
            'starts_at' => '2026-09-05 19:00:00',
            'ends_at' => '2026-09-05 22:30:00',
            'is_published' => true,
            'sort_order' => 7,
        ],
        [
            'title' => 'Festival Kreativitas & Pameran Karya',
            'excerpt' => 'Festival tahunan menampilkan pameran karya, panggung hiburan, dan bazar dari komunitas kreatif.',
            'content' => '<p>Festival Kreativitas merupakan ajang ekspresi bagi para anggota dan komunitas kreatif. Tahun ini menampilkan pameran karya, panggung musik, kompetisi fotografi, dan bazar produk lokal.</p><p>Festival ini juga diikuti oleh komunitas dari luar sebagai bentuk kolaborasi yang memperkuat jejaring kreatif.</p>',
            'category' => 'Pertemuan',
            'location' => 'Aula Utama & Halaman',
            'starts_at' => '2026-10-10 09:00:00',
            'ends_at' => '2026-10-11 21:00:00',
            'is_published' => true,
            'sort_order' => 8,
        ],
        [
            'title' => 'Reuni Alumni Angkatan 2010–2015',
            'excerpt' => 'Pertemuan alumni untuk mempererat silaturahmi, berbagi pengalaman, dan berkontribusi bagi almamater.',
            'content' => '<p>Reuni alumni angkatan 2010–2015 hadir kembali setelah dua tahun vakum. Acara ini menjadi momentum untuk mempererat tali silaturahmi antar alumni, berbagi pengalaman karier, serta mendiskusikan kontribusi alumni bagi perkembangan organisasi.</p><p>Tersedia sesi ramah tamah dan malam keakraban bersama seluruh keluarga besar alumni.</p>',
            'category' => 'Pertemuan',
            'location' => 'Gedung Serbaguna',
            'starts_at' => '2026-12-26 09:00:00',
            'ends_at' => '2026-12-27 15:00:00',
            'is_published' => true,
            'sort_order' => 9,
        ],
        [
            'title' => 'Bakti Sosial & Donor Darah',
            'excerpt' => 'Kegiatan bakti sosial berupa donor darah, pembagian sembako, dan layanan kesehatan gratis.',
            'content' => '<p>Setiap tahun, kami menyelenggarakan kegiatan bakti sosial sebagai bentuk kepedulian kepada masyarakat. Kegiatan meliputi donor darah, pembagian sembako, dan pemeriksaan kesehatan gratis.</p><p>Acara ini terbuka untuk umum dan bekerja sama dengan berbagai mitra serta relawan.</p>',
            'category' => 'Bakti Sosial',
            'location' => 'Halaman Depan',
            'starts_at' => '2026-06-28 07:00:00',
            'ends_at' => '2026-06-28 12:00:00',
            'is_published' => true,
            'sort_order' => 10,
        ],
    ];

    public function run(): void
    {
        foreach ($this->events as $data) {
            Event::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
            );
        }
    }
}
