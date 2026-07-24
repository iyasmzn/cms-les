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
            'title' => 'Manajemen Konten',
            'excerpt' => 'Kelola artikel, halaman, dan media dengan editor yang mudah digunakan tanpa perlu keahlian teknis.',
            'content' => '<p>Fitur Manajemen Konten memungkinkan Anda membuat, mengubah, dan menerbitkan konten kapan saja melalui panel admin yang intuitif. Tidak diperlukan pengetahuan pemrograman untuk mulai mengelola website Anda.</p><p>Dengan editor visual dan sistem penjadwalan, Anda dapat mengatur kapan sebuah konten tampil kepada pengunjung.</p><ul><li>Editor teks kaya (rich text) yang mudah digunakan</li><li>Penjadwalan publikasi otomatis</li><li>Pengelolaan kategori dan tag</li><li>Riwayat perubahan konten</li></ul>',
            'icon' => 'book-open',
            'category' => 'Unggulan',
            'is_published' => true,
            'sort_order' => 1,
        ],
        [
            'title' => 'Manajemen Media',
            'excerpt' => 'Unggah dan atur gambar, dokumen, serta berkas lain dalam satu pustaka media yang rapi.',
            'content' => '<p>Pustaka Media membantu Anda menyimpan dan mengelola seluruh aset digital dalam satu tempat. Unggah gambar, dokumen, atau berkas lain dengan mudah dan gunakan kembali di berbagai halaman.</p><p>Fitur ini mendukung pengorganisasian berkas ke dalam album serta pratinjau langsung sebelum digunakan.</p><ul><li>Unggah berkas secara massal</li><li>Pengelompokan ke dalam album</li><li>Pratinjau gambar dan dokumen</li><li>Optimasi ukuran gambar otomatis</li></ul>',
            'icon' => 'academic-cap',
            'category' => 'Fitur',
            'is_published' => true,
            'sort_order' => 2,
        ],
        [
            'title' => 'Halaman & Menu Dinamis',
            'excerpt' => 'Susun struktur halaman dan menu navigasi sesuai kebutuhan tanpa menyentuh kode.',
            'content' => '<p>Bangun struktur website Anda dengan bebas. Buat halaman baru, atur menu navigasi, dan susun urutan tampilan hanya dengan beberapa klik.</p><p>Setiap perubahan langsung tercermin di halaman depan, sehingga Anda memiliki kendali penuh atas tampilan website.</p><ul><li>Pembuatan halaman statis tanpa batas</li><li>Menu navigasi yang dapat disusun ulang</li><li>Pengaturan urutan seksi halaman depan</li><li>Tautan cepat yang dapat dikustomisasi</li></ul>',
            'icon' => 'star',
            'category' => 'Fitur',
            'is_published' => true,
            'sort_order' => 3,
        ],
        [
            'title' => 'Formulir Pendaftaran Online',
            'excerpt' => 'Terima pendaftaran anggota atau peserta langsung dari website dengan alur gelombang dan verifikasi.',
            'content' => '<p>Fitur Formulir Pendaftaran memudahkan Anda menerima pendaftaran secara online. Atur periode dan gelombang pendaftaran, kelola data pendaftar, dan pantau statusnya dari satu dasbor.</p><p>Data yang masuk tersimpan rapi dan dapat diekspor kapan saja untuk kebutuhan administrasi.</p><ul><li>Pengaturan gelombang dan jadwal pendaftaran</li><li>Beragam jalur atau kategori pendaftaran</li><li>Verifikasi dan pengelolaan status pendaftar</li><li>Rekapitulasi data yang mudah diekspor</li></ul>',
            'icon' => 'globe-alt',
            'category' => 'Unggulan',
            'is_published' => true,
            'sort_order' => 4,
        ],
        [
            'title' => 'Katalog & Toko',
            'excerpt' => 'Tampilkan produk atau koleksi dalam katalog rapi lengkap dengan detail, harga, dan stok.',
            'content' => '<p>Fitur Katalog memungkinkan Anda memamerkan produk atau koleksi secara profesional. Setiap item dilengkapi deskripsi, harga, stok, dan gambar.</p><p>Pengunjung dapat menjelajah katalog dengan nyaman, dan pemesanan dapat diarahkan melalui WhatsApp atau kanal lain yang Anda tentukan.</p><ul><li>Detail produk lengkap dengan gambar</li><li>Pengelolaan kategori dan stok</li><li>Integrasi pemesanan via WhatsApp</li><li>Penataan urutan tampilan produk</li></ul>',
            'icon' => 'heart',
            'category' => 'Fitur',
            'is_published' => true,
            'sort_order' => 5,
        ],
        [
            'title' => 'Pengaturan & Tampilan',
            'excerpt' => 'Sesuaikan identitas, warna, font, dan tata letak website agar sesuai dengan brand Anda.',
            'content' => '<p>Personalisasi website Anda dengan mudah. Ubah nama, logo, warna utama, font, hingga urutan seksi pada halaman depan langsung dari panel pengaturan.</p><p>Seluruh pengaturan dirancang agar fleksibel sehingga website dapat menyesuaikan berbagai kebutuhan organisasi.</p><ul><li>Kustomisasi identitas dan kontak</li><li>Pemilihan warna dan tipografi</li><li>Pengaturan tata letak halaman depan</li><li>Integrasi media sosial</li></ul>',
            'icon' => 'wrench-screwdriver',
            'category' => 'Fitur',
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
