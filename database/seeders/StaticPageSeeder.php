<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Seed halaman-halaman statis default.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Informasi Umum',
                'slug' => 'informasi-umum',
                'meta_description' => 'Informasi umum mengenai organisasi kami, sejarah, dan profil singkat.',
                'content' => '<h2>Informasi Umum</h2><p>Selamat datang di halaman informasi umum. Di sini Anda dapat menemukan berbagai informasi dasar mengenai profil, sejarah berdiri, dan perkembangan kami dari masa ke masa.</p><p>Kami berdiri sejak tahun 2015 dan telah melayani ribuan pengguna serta mitra di berbagai bidang. Dengan tim yang berpengalaman dan teknologi modern, kami berkomitmen memberikan layanan terbaik bagi setiap pengguna.</p>',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Sambutan Direktur',
                'slug' => 'sambutan-direktur',
                'meta_description' => 'Sambutan dan pesan dari Direktur untuk seluruh pengguna, mitra, dan masyarakat.',
                'content' => '<h2>Sambutan Direktur</h2><p>Salam hangat,</p><p>Puji syukur kami panjatkan atas perkembangan yang terus kami capai sehingga organisasi ini tumbuh menjadi lebih baik dari waktu ke waktu.</p><p>Sebagai direktur, saya mengucapkan selamat datang kepada seluruh pengguna baru, mitra, dan masyarakat yang telah mempercayakan kebutuhannya kepada kami. Kepercayaan Anda adalah amanah yang kami emban dengan penuh tanggung jawab.</p><p>Kami terus berbenah dan berinovasi agar dapat memberikan layanan terbaik yang bermanfaat, andal, dan mudah digunakan oleh semua kalangan.</p><p>Terima kasih atas dukungan Anda.</p><p><strong>Direktur</strong></p>',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Visi & Misi',
                'slug' => 'visi-misi',
                'meta_description' => 'Visi dan misi kami sebagai panduan dalam memberikan layanan yang bermutu.',
                'content' => '<h2>Visi</h2><p>Menjadi penyedia layanan digital terdepan yang bermanfaat, andal, dan mudah diakses oleh semua kalangan.</p><h2>Misi</h2><ul><li>Menghadirkan layanan berkualitas yang inovatif dan berpusat pada kebutuhan pengguna.</li><li>Membangun budaya kerja yang profesional, kolaboratif, dan berintegritas.</li><li>Mengembangkan produk dan fitur secara berkelanjutan sesuai perkembangan zaman.</li><li>Menjaga keamanan dan privasi data setiap pengguna.</li><li>Menjalin kemitraan yang harmonis dengan pengguna, mitra, dan masyarakat.</li></ul><h2>Tujuan</h2><p>Mewujudkan pengalaman digital yang memudahkan setiap orang untuk hadir, tumbuh, dan berkembang secara online dengan percaya diri.</p>',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Informasi Lokasi',
                'slug' => 'informasi-lokasi',
                'meta_description' => 'Alamat, peta lokasi, dan informasi cara menuju kantor kami.',
                'content' => '<h2>Lokasi Kantor</h2><p>Kantor kami berlokasi di pusat kota yang mudah dijangkau dengan berbagai moda transportasi umum maupun pribadi.</p><h3>Alamat Lengkap</h3><p>Jl. Merdeka No. 1, Kelurahan Maju Jaya, Kecamatan Sejahtera, Jakarta 10110</p><h3>Kontak</h3><ul><li><strong>Telepon:</strong> (021) 1234-5678</li><li><strong>Email:</strong> info@demo.test</li></ul><h3>Jam Operasional</h3><ul><li>Senin – Jumat: 09.00 – 17.00 WIB</li><li>Sabtu – Minggu: Tutup</li></ul><h3>Petunjuk Arah</h3><p>Dari pusat kota, ikuti Jl. Utama ke arah utara ± 2 km, kemudian belok kiri di perempatan lampu merah menuju Jl. Merdeka. Kantor kami terletak di sebelah kanan jalan, bersebelahan dengan Taman Kota.</p>',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($pages as $data) {
            StaticPage::firstOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}
