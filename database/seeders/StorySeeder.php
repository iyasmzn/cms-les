<?php

namespace Database\Seeders;

use App\Models\Story;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StorySeeder extends Seeder
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $stories = [
        [
            'title' => 'Membangun Website Pertama Tanpa Coding',
            'author_name' => 'Fauzan Ramadhan',
            'author_class' => 'Pemilik UMKM',
            'author_year' => '2023',
            'excerpt' => 'Saya tidak punya latar belakang teknis. Dalam seminggu, website toko saya sudah tayang dan bisa saya kelola sendiri.',
            'content' => '<p>Nama saya Fauzan, pemilik usaha kecil di bidang kuliner. Selama ini saya berpikir membuat website itu rumit dan mahal, apalagi saya sama sekali tidak mengerti pemrograman.</p><p>Ketika pertama kali mencoba platform ini, saya sempat ragu. Tapi ternyata semuanya jauh lebih mudah dari yang saya bayangkan. Cukup mengisi formulir, mengunggah gambar produk, dan menyusun menu — website saya langsung tayang.</p><p>Yang paling saya suka, saya bisa memperbarui katalog dan menulis kabar terbaru kapan saja tanpa perlu bantuan orang lain. Dalam seminggu, toko online saya sudah aktif dan mulai menerima pesanan.</p><p>Kini website menjadi etalase utama usaha saya. Terima kasih, karena teknologi yang tadinya terasa jauh kini bisa saya kuasai sendiri.</p>',
            'is_published' => true,
            'published_at' => '2023-08-15 10:00:00',
            'sort_order' => 1,
        ],
        [
            'title' => 'Mengelola Konten Komunitas Jadi Lebih Rapi',
            'author_name' => 'Hasan Pradana',
            'author_class' => 'Admin Komunitas',
            'author_year' => '2024',
            'excerpt' => 'Sebagai admin komunitas, saya butuh alat yang rapi untuk mengelola artikel dan agenda. Platform ini menjawab semuanya.',
            'content' => '<p>Saya mengelola sebuah komunitas dengan ratusan anggota. Dulu, informasi tersebar di berbagai grup chat dan sulit dilacak. Agenda kegiatan sering terlewat, artikel pun tidak terdokumentasi dengan baik.</p><p>Sejak menggunakan platform ini, semuanya menjadi terpusat. Saya bisa menerbitkan artikel, menjadwalkan agenda, dan mengelola pendaftaran acara dari satu tempat.</p><p>Anggota komunitas kini lebih mudah menemukan informasi yang mereka butuhkan. Tingkat kehadiran di setiap kegiatan pun meningkat karena agenda tampil jelas di halaman depan.</p><p>Bagi saya, kerapian pengelolaan adalah kunci. Dan platform ini membuat pekerjaan admin jauh lebih ringan.</p>',
            'is_published' => true,
            'published_at' => '2024-02-20 09:00:00',
            'sort_order' => 2,
        ],
        [
            'title' => 'Dari Ide Menjadi Portal Berita Komunitas',
            'author_name' => 'Siti Maryam',
            'author_class' => 'Pengelola Media',
            'author_year' => '2025',
            'excerpt' => 'Berawal dari ide sederhana, kini kami punya portal berita yang dikelola tim redaksi komunitas dengan bangga.',
            'content' => '<p>Semuanya berawal dari ide sederhana: mendokumentasikan kegiatan dan prestasi di lingkungan kami agar bisa dibaca lebih banyak orang.</p><p>Dengan platform ini, ide itu terwujud menjadi portal berita yang dikelola sepenuhnya oleh tim kami. Setiap anggota bisa menulis, menyunting, dan menerbitkan artikel sesuai perannya masing-masing.</p><p>Fitur kategori dan penjadwalan membuat alur kerja redaksi menjadi profesional. Kami bahkan bisa menjadwalkan artikel untuk terbit di waktu tertentu.</p><p>Kini portal kami menjadi rujukan informasi yang dibanggakan. Pelajaran terbesar saya: alat yang tepat bisa mengubah ide kecil menjadi karya yang berdampak.</p>',
            'is_published' => true,
            'published_at' => '2025-03-10 08:00:00',
            'sort_order' => 3,
        ],
        [
            'title' => 'Formulir Pendaftaran Online Menghemat Waktu Kami',
            'author_name' => 'Ahmad Ridho',
            'author_class' => 'Panitia Acara',
            'author_year' => '2025',
            'excerpt' => 'Dulu pendaftaran peserta dilakukan manual dan melelahkan. Sekarang semuanya otomatis dan datanya rapi.',
            'content' => '<p>Setiap kali menyelenggarakan acara, urusan pendaftaran selalu menjadi mimpi buruk. Formulir kertas, data yang tercecer, dan rekap manual yang memakan waktu berhari-hari.</p><p>Ketika kami memutuskan menggunakan fitur pendaftaran online di platform ini, beban itu langsung berkurang drastis. Peserta cukup mengisi formulir dari ponsel mereka, dan data langsung tersimpan rapi.</p><p>Kami bisa memantau jumlah pendaftar secara real-time, membagi peserta ke dalam gelombang, dan mengekspor data kapan saja untuk kebutuhan panitia.</p><p>Waktu yang dulu habis untuk administrasi kini bisa kami alihkan untuk mempersiapkan acara agar lebih berkualitas.</p>',
            'is_published' => true,
            'published_at' => '2025-07-22 11:00:00',
            'sort_order' => 4,
        ],
        [
            'title' => 'Website yang Membuat Organisasi Kami Terlihat Profesional',
            'author_name' => 'Abdullah Karim',
            'author_class' => 'Ketua Organisasi',
            'author_year' => '2024',
            'excerpt' => 'Tampilan yang rapi dan modern membuat organisasi kami lebih dipercaya oleh mitra dan calon anggota.',
            'content' => '<p>Sebagai organisasi yang baru berkembang, kredibilitas adalah segalanya. Calon mitra dan anggota sering menilai kami dari kesan pertama, dan website adalah wajah pertama itu.</p><p>Sebelumnya kami hanya mengandalkan media sosial, yang terasa kurang meyakinkan untuk urusan formal. Setelah memiliki website resmi dengan tampilan yang rapi dan modern, respons yang kami terima berubah drastis.</p><p>Mitra lebih percaya, calon anggota lebih tertarik, dan informasi organisasi tersaji dengan profesional. Semua itu kami kelola sendiri tanpa biaya pengembangan yang besar.</p><p>Website ini benar-benar mengangkat citra organisasi kami ke level yang lebih tinggi.</p>',
            'is_published' => true,
            'published_at' => '2024-11-05 14:00:00',
            'sort_order' => 5,
        ],
        [
            'title' => 'Belajar Mandiri Mengelola Website di Usia 50',
            'author_name' => 'Fatimah Nur',
            'author_class' => 'Pegiat Sosial',
            'author_year' => '2022',
            'excerpt' => 'Saya kira teknologi hanya untuk anak muda. Ternyata siapa pun bisa, asalkan alatnya mudah digunakan.',
            'content' => '<p>Di usia 50 tahun, saya pikir dunia teknologi bukan lagi tempat saya. Anak-anak saya yang biasa mengurus segala hal berbau digital.</p><p>Tapi ketika saya ingin membuat wadah untuk kegiatan sosial yang saya rintis, saya memberanikan diri mencoba mengelola website sendiri.</p><p>Ternyata antarmukanya sangat ramah. Menu-menunya jelas, dan saya bisa belajar sedikit demi sedikit. Kini saya bisa menulis kabar kegiatan, mengunggah foto, dan bahkan mengelola donasi dari para donatur.</p><p>Pelajaran berharga bagi saya: usia bukan penghalang untuk belajar hal baru, selama kita mau mencoba dan alatnya memang dirancang untuk memudahkan.</p>',
            'is_published' => true,
            'published_at' => '2022-09-18 09:30:00',
            'sort_order' => 6,
        ],
        [
            'title' => 'Mengelola Toko dan Blog dalam Satu Platform',
            'author_name' => 'Zainab Aini',
            'author_class' => 'Kreator Konten',
            'author_year' => '2023',
            'excerpt' => 'Saya menjalankan toko sekaligus menulis blog. Ternyata keduanya bisa saya kelola dari satu tempat.',
            'content' => '<p>Banyak orang bertanya bagaimana saya bisa mengurus toko online sekaligus rutin menulis blog. Jawabannya: saya memakai satu platform untuk keduanya.</p><p>Dari satu dasbor, saya mengelola katalog produk sekaligus menerbitkan artikel. Tidak perlu berpindah-pindah aplikasi atau membayar banyak layanan berbeda.</p><p>Manajemen waktu jadi lebih efisien. Pagi untuk memperbarui stok produk, siang untuk menulis artikel, dan sisanya untuk melayani pelanggan.</p><p>Dengan integrasi yang mulus, toko dan blog saya saling mendukung. Artikel menarik pengunjung, dan pengunjung menjadi pembeli. Semuanya berjalan harmonis dalam satu ekosistem.</p>',
            'is_published' => true,
            'published_at' => '2023-10-01 10:00:00',
            'sort_order' => 7,
        ],
        [
            'title' => 'Kolaborasi Tim Jadi Lebih Mudah',
            'author_name' => 'Umar Faruq',
            'author_class' => 'Manajer Proyek',
            'author_year' => '2021',
            'excerpt' => 'Dengan hak akses yang bisa diatur per anggota, tim kami bekerja lebih tertib tanpa saling tumpang tindih.',
            'content' => '<p>Mengelola konten bersama tim dulu sering menimbulkan kekacauan. Siapa mengubah apa, kapan, dan mengapa — sering kali tidak jelas.</p><p>Setelah menggunakan platform ini, setiap anggota tim mendapat peran dan hak akses sesuai tugasnya. Penulis fokus menulis, editor fokus menyunting, dan admin mengatur keseluruhan.</p><p>Sistem peran ini menghilangkan kebingungan dan mencegah kesalahan. Setiap orang tahu batas tanggung jawabnya, dan pekerjaan berjalan tertib.</p><p>Kolaborasi yang dulu terasa rumit kini menjadi lancar. Produktivitas tim meningkat, dan kualitas konten yang kami hasilkan pun semakin baik.</p>',
            'is_published' => true,
            'published_at' => '2021-04-12 08:00:00',
            'sort_order' => 8,
        ],
    ];

    public function run(): void
    {
        foreach ($this->stories as $data) {
            Story::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data
            );
        }
    }
}
