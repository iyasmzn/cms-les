<?php

namespace Database\Seeders;

use App\Models\Donation;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        if (Donation::exists()) {
            return;
        }

        $donations = [
            [
                'name' => 'Abdurrahman Wahid',
                'email' => 'abdurrahman@gmail.com',
                'phone' => '081234567890',
                'is_anonymous' => false,
                'amount' => 1000000,
                'payment_method' => 'transfer_bank',
                'message' => 'Semoga bermanfaat dan terus berkembang. Sukses selalu!',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(30),
            ],
            [
                'name' => 'Fatimah Az-Zahra',
                'email' => null,
                'phone' => '082345678901',
                'is_anonymous' => false,
                'amount' => 500000,
                'payment_method' => 'transfer_bank',
                'message' => 'Semoga dukungan kecil ini bermanfaat untuk banyak orang.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(25),
            ],
            [
                'name' => 'Anonim',
                'email' => null,
                'phone' => null,
                'is_anonymous' => true,
                'amount' => 250000,
                'payment_method' => 'qris',
                'message' => 'Terima kasih atas layanannya.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(20),
            ],
            [
                'name' => 'Muhamad Rizki',
                'email' => 'mrizki@yahoo.com',
                'phone' => '083456789012',
                'is_anonymous' => false,
                'amount' => 200000,
                'payment_method' => 'transfer_bank',
                'message' => 'Semoga proyek ini semakin maju dan bermanfaat luas.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(18),
            ],
            [
                'name' => 'Komunitas Kreatif 2015',
                'email' => 'komunitas2015@gmail.com',
                'phone' => '084567890123',
                'is_anonymous' => false,
                'amount' => 150000,
                'payment_method' => 'qris',
                'message' => 'Terima kasih atas manfaatnya. Ini bentuk dukungan kami.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(15),
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'sitaminah@gmail.com',
                'phone' => '085678901234',
                'is_anonymous' => false,
                'amount' => 500000,
                'payment_method' => 'tunai',
                'message' => null,
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(12),
            ],
            [
                'name' => 'Keluarga Besar Pak Hasan',
                'email' => null,
                'phone' => '086789012345',
                'is_anonymous' => false,
                'amount' => 2000000,
                'payment_method' => 'transfer_bank',
                'message' => 'Dukungan untuk pengembangan fasilitas dan program.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(10),
            ],
            [
                'name' => 'Anonim',
                'email' => null,
                'phone' => null,
                'is_anonymous' => true,
                'amount' => 100000,
                'payment_method' => 'qris',
                'message' => 'Semoga sukses selalu.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(8),
            ],
            [
                'name' => 'Bapak Dermawan',
                'email' => 'dermawan@gmail.com',
                'phone' => '087890123456',
                'is_anonymous' => false,
                'amount' => 300000,
                'payment_method' => 'transfer_bank',
                'message' => 'Untuk program bantuan bagi yang membutuhkan.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(5),
            ],
            [
                'name' => 'Ibu Nurhasanah',
                'email' => 'nurhasanah@gmail.com',
                'phone' => '088901234567',
                'is_anonymous' => false,
                'amount' => 150000,
                'payment_method' => 'qris',
                'message' => null,
                'status' => 'pending',
                'confirmed_at' => null,
            ],
            [
                'name' => 'Bapak Wahyu Prasetyo',
                'email' => 'wahyuprasetyo@gmail.com',
                'phone' => '089012345678',
                'is_anonymous' => false,
                'amount' => 250000,
                'payment_method' => 'transfer_bank',
                'message' => 'Semoga dibalas kebaikan berlipat ganda.',
                'status' => 'pending',
                'confirmed_at' => null,
            ],
            [
                'name' => 'Anonim',
                'email' => null,
                'phone' => '081298765432',
                'is_anonymous' => true,
                'amount' => 50000,
                'payment_method' => 'tunai',
                'message' => null,
                'status' => 'pending',
                'confirmed_at' => null,
            ],
        ];

        foreach ($donations as $data) {
            Donation::create($data);
        }
    }
}
