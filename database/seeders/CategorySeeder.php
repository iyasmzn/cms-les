<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = $this->defaults();

        foreach ($defaults as $type => $names) {
            foreach ($names as $order => $name) {
                Category::firstOrCreate(
                    ['type' => $type, 'name' => $name],
                    ['sort_order' => $order + 1, 'is_active' => true],
                );
            }
        }

        $this->captureExistingTeacherPositions();
    }

    /**
     * Register any jabatan already used by existing teachers as categories, so
     * the dynamic Select keeps offering them even when they are not part of the
     * default list. Idempotent and safe to run repeatedly.
     */
    private function captureExistingTeacherPositions(): void
    {
        $order = (int) Category::forType(Category::TYPE_TEACHER)->max('sort_order');

        Teacher::query()
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->distinct()
            ->orderBy('position')
            ->pluck('position')
            ->each(function (string $name) use (&$order): void {
                Category::firstOrCreate(
                    ['type' => Category::TYPE_TEACHER, 'name' => $name],
                    ['sort_order' => ++$order, 'is_active' => true],
                );
            });
    }

    /** @return array<string, string[]> */
    private function defaults(): array
    {
        return [
            // ── Blog / Berita ─────────────────────────────────────
            Category::TYPE_POST => [
                'Berita',
                'Pengumuman',
                'Prestasi',
                'Kegiatan',
                'Tips & Panduan',
                'Inspirasi',
                'Lainnya',
            ],

            // ── Produk / Katalog Toko ──────────────────────────────
            Category::TYPE_PRODUCT => [
                'Elektronik',
                'Fashion',
                'Rumah Tangga',
                'Olahraga',
                'Aksesoris',
                'Perlengkapan Kantor',
                'Umum',
            ],

            // ── Kegiatan / Event ───────────────────────────────────
            Category::TYPE_EVENT => [
                'Seminar',
                'Workshop',
                'Webinar',
                'Lomba',
                'Pertemuan',
                'Peringatan Hari Besar',
                'Bakti Sosial',
                'Lainnya',
            ],

            // ── Program / Fitur ────────────────────────────────────
            Category::TYPE_PROGRAM => [
                'Unggulan',
                'Fitur',
                'Layanan',
                'Lainnya',
            ],

            // ── Unduhan ────────────────────────────────────────────
            Category::TYPE_DOWNLOAD => [
                'Formulir',
                'Surat Edaran',
                'Pengumuman',
                'Panduan',
                'Administrasi',
                'Kalender',
                'Lainnya',
            ],

            // ── Guru / Jabatan ─────────────────────────────────────
            Category::TYPE_TEACHER => [
                'Kepala Sekolah',
                'Wakil Kepala Sekolah',
                'Guru',
                'Guru BK',
                'Wali Kelas',
                'Pembina Ekstrakurikuler',
                'Staf Tata Usaha',
                'Lainnya',
            ],
        ];
    }
}
