<?php

namespace Database\Seeders;

use App\Models\Download;
use Illuminate\Database\Seeder;

class DownloadSeeder extends Seeder
{
    public function run(): void
    {
        if (Download::exists()) {
            return;
        }

        $downloads = [
            [
                'title' => 'Formulir Pendaftaran Anggota 2026',
                'description' => 'Formulir pendaftaran resmi untuk calon anggota baru periode 2026.',
                'category' => 'Formulir',
                'file_path' => 'downloads/formulir-pendaftaran-2026.pdf',
                'original_filename' => 'Formulir-Pendaftaran-2026.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 524288,
                'download_count' => 0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Kalender Kegiatan Tahun 2026',
                'description' => 'Jadwal lengkap agenda, acara, dan kegiatan penting sepanjang tahun 2026.',
                'category' => 'Kalender',
                'file_path' => 'downloads/kalender-kegiatan-2026.pdf',
                'original_filename' => 'Kalender-Kegiatan-2026.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 786432,
                'download_count' => 0,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Surat Edaran Tata Tertib Anggota',
                'description' => 'Peraturan dan tata tertib yang wajib dipatuhi oleh seluruh anggota.',
                'category' => 'Surat Edaran',
                'file_path' => 'downloads/tata-tertib-anggota.pdf',
                'original_filename' => 'Tata-Tertib-Anggota.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 409600,
                'download_count' => 0,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Profil & Company Profile 2026',
                'description' => 'Dokumen profil lengkap organisasi, visi misi, layanan, dan pencapaian.',
                'category' => 'Administrasi',
                'file_path' => 'downloads/company-profile-2026.pdf',
                'original_filename' => 'Company-Profile-2026.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 307200,
                'download_count' => 0,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Pengumuman Hasil Seleksi Tahap 1',
                'description' => 'Pengumuman resmi hasil seleksi pendaftaran tahap pertama periode 2026.',
                'category' => 'Pengumuman',
                'file_path' => 'downloads/pengumuman-seleksi-tahap-1.pdf',
                'original_filename' => 'Pengumuman-Seleksi-Tahap-1.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 262144,
                'download_count' => 0,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Formulir Permohonan Surat Keterangan',
                'description' => 'Formulir untuk mengajukan permohonan surat keterangan bagi anggota yang membutuhkan.',
                'category' => 'Formulir',
                'file_path' => 'downloads/formulir-surat-keterangan.pdf',
                'original_filename' => 'Formulir-Surat-Keterangan.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 204800,
                'download_count' => 0,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Panduan Penggunaan Website',
                'description' => 'Panduan lengkap cara mengakses dan menggunakan seluruh fitur pada website ini.',
                'category' => 'Panduan',
                'file_path' => 'downloads/panduan-penggunaan-website.pdf',
                'original_filename' => 'Panduan-Penggunaan-Website.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 1048576,
                'download_count' => 0,
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($downloads as $data) {
            Download::create($data);
        }
    }
}
