<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Institution;
use App\Models\RegistrationWave;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // Demo waves are attached to the first jenjang; other jenjang start
        // empty for the admin to configure.
        $institutionId = Institution::query()->orderBy('sort_order')->orderBy('id')->value('id');

        $active = AcademicYear::updateOrCreate(
            ['year_start' => 2026, 'year_end' => 2027],
            ['is_active' => true],
        );

        $previous = AcademicYear::updateOrCreate(
            ['year_start' => 2025, 'year_end' => 2026],
            ['is_active' => false],
        );

        // Gelombang tahun ajaran aktif: satu berjalan, satu akan datang.
        $this->seedWaves($active, $institutionId, [
            [
                'name' => 'Gelombang 1',
                'start_date' => $today->copy()->subDays(7),
                'end_date' => $today->copy()->addDays(21),
                'selection_date' => $today->copy()->addDays(26),
                'announcement_date' => $today->copy()->addDays(33),
            ],
            [
                'name' => 'Gelombang 2',
                'start_date' => $today->copy()->addDays(22),
                'end_date' => $today->copy()->addDays(50),
                'selection_date' => $today->copy()->addDays(55),
                'announcement_date' => $today->copy()->addDays(62),
            ],
        ]);

        // Gelombang tahun ajaran sebelumnya: keduanya sudah selesai.
        $this->seedWaves($previous, $institutionId, [
            [
                'name' => 'Gelombang 1',
                'start_date' => $today->copy()->subYear()->subDays(40),
                'end_date' => $today->copy()->subYear()->subDays(12),
                'selection_date' => $today->copy()->subYear()->subDays(7),
                'announcement_date' => $today->copy()->subYear(),
            ],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $waves
     */
    private function seedWaves(AcademicYear $year, ?int $institutionId, array $waves): void
    {
        foreach ($waves as $wave) {
            RegistrationWave::updateOrCreate(
                ['academic_year_id' => $year->id, 'name' => $wave['name']],
                array_merge($wave, ['institution_id' => $institutionId, 'is_active' => true]),
            );
        }
    }
}
