<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        if (Teacher::exists()) {
            return;
        }

        $teachers = [
            [
                'name' => 'Ahmad Fauzi, M.M.',
                'nip' => 'EMP-001',
                'position' => 'Chief Executive Officer',
                'subject' => 'Kepemimpinan & Strategi',
                'education' => 'S2 Manajemen Bisnis',
                'phone' => '(021) 1234-5678',
                'email' => 'ahmad.fauzi@demo.test',
                'whatsapp' => '6281234567890',
                'sort_order' => 1,
            ],
            [
                'name' => 'Siti Rahayu, M.Kom.',
                'nip' => 'EMP-002',
                'position' => 'Chief Technology Officer',
                'subject' => 'Teknologi & Produk',
                'education' => 'S2 Ilmu Komputer',
                'phone' => '(021) 1234-5679',
                'email' => 'siti.rahayu@demo.test',
                'whatsapp' => '6281234567891',
                'sort_order' => 2,
            ],
            [
                'name' => 'Budi Santoso, S.Kom.',
                'nip' => 'EMP-003',
                'position' => 'Lead Developer',
                'subject' => 'Pengembangan Aplikasi',
                'education' => 'S1 Teknik Informatika',
                'phone' => null,
                'email' => 'budi.santoso@demo.test',
                'whatsapp' => '6281234567892',
                'sort_order' => 3,
            ],
            [
                'name' => 'Dewi Lestari, S.Ds.',
                'nip' => 'EMP-004',
                'position' => 'UI/UX Designer',
                'subject' => 'Desain Produk',
                'education' => 'S1 Desain Komunikasi Visual',
                'phone' => null,
                'email' => 'dewi.lestari@demo.test',
                'whatsapp' => '6281234567893',
                'sort_order' => 4,
            ],
            [
                'name' => 'Hendra Gunawan, S.E.',
                'nip' => 'EMP-005',
                'position' => 'Marketing Manager',
                'subject' => 'Pemasaran Digital',
                'education' => 'S1 Manajemen Pemasaran',
                'phone' => null,
                'email' => 'hendra.gunawan@demo.test',
                'whatsapp' => null,
                'sort_order' => 5,
            ],
            [
                'name' => 'Rina Marlina, S.I.Kom.',
                'nip' => 'EMP-006',
                'position' => 'Content Strategist',
                'subject' => 'Konten & Komunikasi',
                'education' => 'S1 Ilmu Komunikasi',
                'phone' => null,
                'email' => 'rina.marlina@demo.test',
                'whatsapp' => '6281234567895',
                'sort_order' => 6,
            ],
            [
                'name' => 'Agus Permana, S.Kom.',
                'nip' => 'EMP-007',
                'position' => 'Customer Support Lead',
                'subject' => 'Layanan Pelanggan',
                'education' => 'S1 Sistem Informasi',
                'phone' => null,
                'email' => 'agus.permana@demo.test',
                'whatsapp' => '6281234567896',
                'sort_order' => 7,
            ],
            [
                'name' => 'Nurul Hidayah, S.Ak.',
                'nip' => 'EMP-008',
                'position' => 'Finance & Administration',
                'subject' => 'Keuangan & Administrasi',
                'education' => 'S1 Akuntansi',
                'phone' => null,
                'email' => 'nurul.hidayah@demo.test',
                'whatsapp' => '6281234567897',
                'sort_order' => 8,
            ],
        ];

        foreach ($teachers as $data) {
            Teacher::create(array_merge($data, ['is_active' => true]));
        }
    }
}
