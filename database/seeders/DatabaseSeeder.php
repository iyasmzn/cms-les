<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Dummy public users
        $dummyUsers = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@example.com'],
            ['name' => 'Siti Rahmah', 'email' => 'siti@example.com'],
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com'],
        ];

        foreach ($dummyUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(InstitutionSeeder::class);
        $this->call(PpdbFieldSeeder::class);
        $this->call(AdmissionPathSeeder::class);
        $this->call(AcademicYearSeeder::class);
        $this->call(SpmbRegistrationSeeder::class);
        $this->call(StatSeeder::class);
        $this->call(TeacherSeeder::class);
        $this->call(SlideSeeder::class);
        $this->call(ContactItemSeeder::class);
        $this->call(StaticPageSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(TestimonialSeeder::class);
        $this->call(GreetingSeeder::class);
        $this->call(DownloadSeeder::class);
        $this->call(ShieldSeeder::class);
    }
}
