<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed semua settings default aplikasi.
     * Menggunakan firstOrCreate agar tidak menimpa data yang sudah diubah admin.
     */
    public function run(): void
    {
        $defaults = $this->defaults();

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /** @return array<string, mixed> */
    private function defaults(): array
    {
        return array_merge(
            $this->general(),
            $this->navbar(),
            $this->landingPage(),
            $this->quickLinks(),
            $this->footer(),
            $this->spmb(),
            $this->theme(),
        );
    }

    // ── Pengaturan Umum ──────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function general(): array
    {
        return [
            'site_name' => 'Demo CMS',
            'site_tagline' => 'Kelola Website Anda dengan Mudah',
            'site_description' => 'Website demo yang menampilkan fitur-fitur CMS: manajemen konten, blog, agenda kegiatan, katalog, formulir pendaftaran, dan lainnya dalam satu platform.',

            'contact_address' => 'Jl. Merdeka No. 1, Jakarta 10110',
            'contact_phone' => '(021) 123-4567',
            'contact_email' => 'info@demo.test',
            'contact_hours' => 'Senin–Jumat, 09.00–17.00 WIB',
            'contact_map_url' => null,

            'social_facebook' => null,
            'social_instagram' => null,
            'social_youtube' => null,
            'social_tiktok' => null,
            'social_telegram' => null,
            'social_whatsapp' => null,

            // Nomor WA khusus untuk pesanan buku (toko)
            'shop_whatsapp' => null,
        ];
    }

    // ── Menu Navigasi ────────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function navbar(): array
    {
        return [
            'nav_items' => json_encode([
                ['label' => 'Beranda',  'url' => '/',              'target' => '_self', 'is_active' => true, 'children' => []],
                ['label' => 'Courses',  'url' => '/courses',       'target' => '_self', 'is_active' => true, 'children' => []],
                ['label' => 'Layanan',  'url' => '/program',       'target' => '_self', 'is_active' => true, 'children' => []],
                ['label' => 'Agenda',   'url' => '/kegiatan',      'target' => '_self', 'is_active' => true, 'children' => []],
                ['label' => 'Blog',     'url' => '/blog',          'target' => '_self', 'is_active' => true, 'children' => []],
                ['label' => 'Kontak',   'url' => '#kontak',        'target' => '_self', 'is_active' => true, 'children' => []],
            ]),
        ];
    }

    // ── Urutan Seksi Halaman Depan ───────────────────────────────────

    /** @return array<string, mixed> */
    private function landingPage(): array
    {
        return [
            'section_order' => json_encode([
                ['key' => 'section_hero',         'visible' => true],
                ['key' => 'section_quick_links',  'visible' => true],
                ['key' => 'section_courses',      'visible' => true],
                ['key' => 'section_stats',        'visible' => true],
                ['key' => 'section_principal',    'visible' => true],
                ['key' => 'section_programs',     'visible' => true],
                ['key' => 'section_events',       'visible' => true],
                ['key' => 'section_products',     'visible' => true],
                ['key' => 'section_gallery',      'visible' => true],
                ['key' => 'section_blog',         'visible' => true],
                ['key' => 'section_testimonials', 'visible' => true],
                ['key' => 'section_spmb',         'visible' => false],
                ['key' => 'section_spmb_steps',   'visible' => false],
                ['key' => 'section_contact',      'visible' => true],
            ]),
        ];
    }

    // ── Tautan Cepat ─────────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function quickLinks(): array
    {
        return [
            'quick_links' => json_encode([
                ['icon' => '🏊', 'label' => 'Courses',     'url' => '/courses',       'is_active' => true],
                ['icon' => '🗓', 'label' => 'Jadwal',      'url' => '/my-courses',    'is_active' => true],
                ['icon' => '🎓', 'label' => 'Layanan',     'url' => '/program',       'is_active' => true],
                ['icon' => '📚', 'label' => 'Katalog',     'url' => '/buku',          'is_active' => true],
                ['icon' => '📞', 'label' => 'Kontak',      'url' => '#kontak',        'is_active' => true],
            ]),
        ];
    }

    // ── Footer ───────────────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function footer(): array
    {
        return [
            'footer_services_enabled' => '1',
            'footer_services_title' => 'Layanan',
            'footer_service_links' => json_encode([
                ['label' => 'Toko Buku',       'url' => '/buku',      'feature' => 'toko', 'is_active' => true],
                ['label' => 'Daftar Santri',   'url' => '/ppdb',      'feature' => '',     'is_active' => true],
                ['label' => 'Blog & Berita',   'url' => '/blog',      'feature' => '',     'is_active' => true],
                ['label' => 'Unduhan',         'url' => '/unduhan',   'feature' => '',       'is_active' => true],
                ['label' => 'Tenaga Pendidik', 'url' => '/guru',      'feature' => '',       'is_active' => true],
                ['label' => 'Keranjang',       'url' => '/keranjang', 'feature' => 'toko',   'is_active' => true],
            ]),
            'footer_copyright' => '',
            'footer_credit_enabled' => '1',
            'footer_credit_text' => 'Dibuat dengan penuh semangat',
        ];
    }

    // ── Pendaftaran Online ───────────────────────────────────────────

    /** @return array<string, mixed> */
    private function spmb(): array
    {
        $procedures = [
            ['icon' => '📝', 'title' => 'Isi Formulir Online',  'description' => 'Kunjungi halaman pendaftaran dan isi formulir dengan data diri secara lengkap dan benar.'],
            ['icon' => '📁', 'title' => 'Siapkan Berkas',       'description' => 'Persiapkan dokumen pendukung: kartu identitas, pas foto terbaru, dan berkas lain sesuai persyaratan.'],
            ['icon' => '✅', 'title' => 'Verifikasi Data',      'description' => 'Tim kami akan memverifikasi kelengkapan berkas dan menghubungi Anda untuk tahap selanjutnya.'],
            ['icon' => '🎉', 'title' => 'Pengumuman Hasil',     'description' => 'Hasil pendaftaran diumumkan melalui website resmi dan dikirimkan langsung via email atau WhatsApp.'],
        ];

        $fees = [
            ['category' => 'Biaya Pendaftaran', 'amount' => 'Rp 100.000', 'note' => 'Tidak dikembalikan'],
            ['category' => 'Biaya Registrasi',  'amount' => 'Rp 500.000', 'note' => 'Sekali bayar di awal'],
            ['category' => 'Iuran Bulanan',     'amount' => 'Rp 150.000', 'note' => 'Dibayar setiap bulan'],
            ['category' => 'Paket Materi',      'amount' => 'Rp 250.000', 'note' => 'Opsional, sesuai kebutuhan'],
        ];

        return [
            // Kartu di halaman depan
            'spmb_card_title' => 'Pendaftaran {year} Telah Dibuka!',
            'spmb_card_description' => 'Segera daftarkan diri Anda dan bergabung bersama kami. Isi formulir pendaftaran online sebelum batas waktu berakhir.',
            'spmb_card_cta_label' => 'Daftar Sekarang',
            'spmb_card_cta_url' => '/ppdb',
            'spmb_card_secondary_label' => 'Info Selengkapnya',

            // Section tahapan di halaman depan
            'spmb_steps_title' => 'Alur Pendaftaran',
            'spmb_steps_description' => 'Ikuti langkah-langkah berikut untuk menyelesaikan proses pendaftaran Anda.',
            'spmb_steps_cta_label' => 'Lihat Detail & Daftar',
            'spmb_steps_cta_url' => '/ppdb',

            // Form pendaftaran
            'spmb_form_enabled' => 1,
            'spmb_form_title' => 'Formulir Pendaftaran',
            'spmb_form_description' => 'Isi formulir di bawah ini dengan data yang benar dan lengkap. Tim kami akan menghubungi Anda untuk proses verifikasi.',
            'spmb_closed_message' => 'Pendaftaran saat ini sedang ditutup. Pantau informasi terbaru melalui halaman ini atau hubungi kami.',

            // Konten prosedur & biaya
            'spmb_procedures' => json_encode($procedures),
            'spmb_fees' => json_encode($fees),
        ];
    }

    // ── Tema & Tampilan ───────────────────────────────────────────────

    /** @return array<string, mixed> */
    private function theme(): array
    {
        return [
            'theme_primary_color' => '#08484A',
            'theme_font' => 'plus-jakarta-sans',
        ];
    }
}
