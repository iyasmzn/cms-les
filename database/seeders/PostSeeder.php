<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        if (Post::exists()) {
            return;
        }

        $posts = [
            [
                'title' => 'Selamat Datang di Website Demo CMS',
                'slug' => 'selamat-datang-di-website-demo-cms',
                'excerpt' => 'Website demo ini hadir untuk menampilkan seluruh fitur CMS: manajemen konten, blog, agenda, katalog, hingga formulir pendaftaran online dalam satu platform.',
                'content' => '<p>Selamat datang! Website ini adalah demo yang dirancang untuk menampilkan berbagai fitur yang tersedia di dalam CMS. Melalui halaman-halaman contoh ini, Anda dapat melihat bagaimana konten dikelola dan ditampilkan kepada pengunjung.</p><h2>Apa yang Bisa Anda Temukan</h2><p>Di dalam demo ini tersedia beragam modul siap pakai: artikel blog, agenda kegiatan, katalog produk, halaman profil, galeri cerita pengguna, testimoni, formulir pendaftaran, hingga dukungan donasi.</p><p>Semua konten pada website ini hanyalah data contoh (placeholder) yang dapat Anda ubah dengan mudah melalui panel admin. Silakan jelajahi setiap menu untuk merasakan pengalaman lengkapnya.</p>',
                'category' => 'Berita',
                'author' => 'Tim Redaksi',
                'author_initials' => 'TR',
                'read_time' => 2,
                'is_published' => true,
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Pendaftaran Anggota Baru 2026 Resmi Dibuka',
                'slug' => 'pendaftaran-anggota-baru-2026-resmi-dibuka',
                'excerpt' => 'Pendaftaran anggota baru periode 2026 telah dibuka. Simak syarat, biaya, dan alur pendaftaran online lengkapnya di sini.',
                'content' => '<p>Kami dengan senang hati mengumumkan pembukaan pendaftaran anggota baru untuk periode 2026. Pendaftaran dibuka mulai hari ini hingga batas waktu yang ditentukan pada setiap gelombang.</p><h2>Pilihan Paket</h2><ul><li><strong>Paket Dasar</strong> — Akses ke seluruh materi dan agenda reguler.</li><li><strong>Paket Lengkap</strong> — Termasuk sesi pendampingan dan materi tambahan.</li><li><strong>Paket Premium</strong> — Seluruh fasilitas ditambah prioritas dukungan.</li></ul><h2>Persyaratan Umum</h2><p>Calon anggota cukup menyiapkan: kartu identitas, alamat email aktif, nomor telepon, dan pas foto terbaru. Seluruh proses dapat dilakukan secara online melalui halaman pendaftaran.</p><p>Untuk informasi lebih lanjut, silakan hubungi tim kami melalui kontak yang tersedia di website ini.</p>',
                'category' => 'Pengumuman',
                'author' => 'Tim Pendaftaran',
                'author_initials' => 'TP',
                'read_time' => 4,
                'is_published' => true,
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Tim Kami Raih Penghargaan Pelayanan Terbaik 2026',
                'slug' => 'tim-kami-raih-penghargaan-pelayanan-terbaik-2026',
                'excerpt' => 'Kabar membanggakan! Tim kami berhasil meraih penghargaan Pelayanan Terbaik pada ajang penghargaan tahunan yang diselenggarakan di Jakarta.',
                'content' => '<p>Sebuah kabar membanggakan datang dari tim kami. Pada ajang penghargaan tahunan yang diselenggarakan di Jakarta pekan lalu, kami dinobatkan sebagai penerima penghargaan Pelayanan Terbaik kategori organisasi.</p><h2>Buah dari Kerja Keras</h2><p>Penghargaan ini merupakan hasil dari dedikasi seluruh tim dalam memberikan pelayanan yang responsif dan berkualitas kepada setiap anggota. Konsistensi dan komitmen menjadi kunci pencapaian ini.</p><p>"Ini adalah pencapaian bersama. Terima kasih kepada seluruh tim dan dukungan dari para anggota yang selalu setia," ungkap perwakilan tim usai menerima penghargaan.</p><h2>Rencana ke Depan</h2><p>Kami berkomitmen untuk terus meningkatkan kualitas layanan dan menghadirkan inovasi baru demi kepuasan seluruh anggota dan mitra.</p>',
                'category' => 'Prestasi',
                'author' => 'Tim Redaksi',
                'author_initials' => 'TR',
                'read_time' => 4,
                'is_published' => true,
                'published_at' => now()->subDays(14),
            ],
            [
                'title' => 'Gelaran Temu Anggota Tahunan Berlangsung Meriah',
                'slug' => 'temu-anggota-tahunan-berlangsung-meriah',
                'excerpt' => 'Acara Temu Anggota Tahunan berlangsung meriah dan penuh kehangatan, dihadiri ratusan peserta dari berbagai daerah.',
                'content' => '<p>Acara Temu Anggota Tahunan telah berlangsung dengan meriah pada Sabtu lalu. Ratusan peserta dari berbagai daerah hadir untuk mengikuti rangkaian kegiatan sepanjang hari.</p><h2>Rangkaian Acara</h2><p>Kegiatan diawali dengan sesi pembukaan dan sambutan, dilanjutkan dengan diskusi panel, sesi berbagi pengalaman, serta pemberian apresiasi kepada anggota teraktif. Puncak acara diisi dengan ramah tamah dan foto bersama.</p><p>Antusiasme peserta terlihat sepanjang acara, terutama pada sesi tanya jawab yang berlangsung interaktif.</p><h2>Apresiasi</h2><p>Panitia menyampaikan terima kasih kepada seluruh peserta dan pihak yang telah mendukung suksesnya acara ini. Sampai jumpa pada gelaran berikutnya!</p>',
                'category' => 'Kegiatan',
                'author' => 'Tim Humas',
                'author_initials' => 'TH',
                'read_time' => 3,
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Program Beasiswa & Bantuan — Daftar Sebelum Batas Waktu',
                'slug' => 'program-beasiswa-dan-bantuan',
                'excerpt' => 'Kami membuka program beasiswa dan bantuan bagi peserta berprestasi maupun yang membutuhkan. Simak cakupan dan persyaratannya.',
                'content' => '<p>Sebagai bagian dari komitmen kami terhadap akses yang inklusif, kami kembali membuka Program Beasiswa & Bantuan untuk periode 2026.</p><h2>Cakupan</h2><p>Program ini mencakup:</p><ul><li>Keringanan atau pembebasan biaya registrasi</li><li>Akses penuh ke seluruh materi dan agenda</li><li>Pendampingan khusus dari tim</li><li>Perlengkapan pendukung sesuai kebutuhan</li></ul><h2>Persyaratan</h2><ul><li>Mengisi formulir pengajuan secara lengkap</li><li>Melampirkan dokumen pendukung yang relevan</li><li>Berkomitmen mengikuti seluruh rangkaian kegiatan</li></ul><p>Pendaftaran dibuka hingga batas waktu yang ditentukan. Hubungi tim kami untuk informasi lebih lanjut.</p>',
                'category' => 'Pengumuman',
                'author' => 'Tim Pendaftaran',
                'author_initials' => 'TP',
                'read_time' => 4,
                'is_published' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => '5 Tips Mengelola Konten Website agar Selalu Segar',
                'slug' => '5-tips-mengelola-konten-website',
                'excerpt' => 'Konten yang terawat membuat website Anda lebih dipercaya dan mudah ditemukan. Berikut lima tips praktis mengelola konten secara konsisten.',
                'content' => '<p>Website yang aktif dan terawat memberi kesan profesional serta membantu peringkat pencarian. Berikut lima tips sederhana untuk menjaga konten Anda tetap segar.</p><h2>1. Jadwalkan Publikasi Rutin</h2><p>Tetapkan kalender konten sederhana, misalnya satu artikel per minggu, agar pembaca punya alasan untuk kembali.</p><h2>2. Perbarui Konten Lama</h2><p>Tinjau artikel lama secara berkala. Perbarui data, tautan, dan gambar agar tetap relevan.</p><h2>3. Gunakan Kategori & Tag</h2><p>Struktur yang rapi memudahkan pengunjung menemukan konten yang mereka cari.</p><h2>4. Optimalkan untuk Pencarian</h2><p>Tulis judul yang jelas, isi deskripsi meta, dan gunakan gambar dengan ukuran wajar agar halaman cepat dimuat.</p><h2>5. Pantau Statistik</h2><p>Perhatikan halaman mana yang paling banyak dikunjungi, lalu buat lebih banyak konten serupa.</p><p>Dengan pengelolaan yang konsisten, website Anda akan terus tumbuh dan memberi nilai bagi pengunjung.</p>',
                'category' => 'Inspirasi',
                'author' => 'Tim Konten',
                'author_initials' => 'TK',
                'read_time' => 5,
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($posts as $data) {
            Post::create($data);
        }
    }
}
