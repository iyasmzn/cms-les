<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        if (Testimonial::exists()) {
            return;
        }

        $testimonials = [
            [
                'name' => 'Rahmat Hidayat',
                'class_year' => '2021',
                'graduation_year' => '2023',
                'message' => 'Platform ini benar-benar mengubah cara kami mengelola website. Antarmukanya mudah dipahami, dan tim kami bisa langsung produktif tanpa pelatihan panjang.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Sari Anggraini',
                'class_year' => '2020',
                'graduation_year' => '2022',
                'message' => 'Yang paling saya suka adalah fleksibilitasnya. Saya bisa menyesuaikan tampilan, menu, dan konten sesuai kebutuhan tanpa harus menyewa developer.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Fajar Nugroho',
                'class_year' => '2019',
                'graduation_year' => '2021',
                'message' => 'Fitur katalog dan blog dalam satu tempat sangat membantu usaha saya. Sekarang semua kebutuhan digital saya tercakup dalam satu platform.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Indah Permatasari',
                'class_year' => '2022',
                'graduation_year' => '2024',
                'message' => 'Formulir pendaftaran online-nya luar biasa memudahkan. Data pendaftar tersimpan rapi dan bisa diekspor kapan saja. Sangat menghemat waktu.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Dimas Prasetyo',
                'class_year' => '2018',
                'graduation_year' => '2020',
                'message' => 'Dukungan tim sangat responsif. Setiap kali ada kendala, selalu ada solusi yang cepat dan jelas. Pelayanan seperti ini yang membuat kami bertahan.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Rini Setiawati',
                'class_year' => '2021',
                'graduation_year' => '2023',
                'message' => 'Website organisasi kami kini terlihat jauh lebih profesional. Mitra dan anggota memberikan respons yang sangat positif sejak website baru diluncurkan.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Andi Maulana',
                'class_year' => '2017',
                'graduation_year' => '2019',
                'message' => 'Saya bukan orang teknis, tapi bisa mengelola seluruh konten website sendiri. Itu pencapaian yang tidak pernah saya bayangkan sebelumnya.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Fitri Handayani',
                'class_year' => '2022',
                'graduation_year' => '2024',
                'message' => 'Pesan saya untuk yang masih ragu: jangan tunda lagi! Platform ini adalah pilihan tepat untuk siapa pun yang ingin hadir secara online dengan mudah dan profesional.',
                'photo' => null,
                'is_published' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::create($data);
        }
    }
}
