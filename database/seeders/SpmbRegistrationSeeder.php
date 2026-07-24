<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\SpmbRegistration;
use Illuminate\Database\Seeder;

class SpmbRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $paths = AdmissionPath::query()->pluck('id')->all();

        if ($paths === []) {
            return;
        }

        // Data contoh — reset agar seeding dapat diulang dengan bersih.
        SpmbRegistration::query()->delete();

        // Tahun ajaran aktif lebih banyak pendaftar; tahun sebelumnya lebih sedikit.
        $years = AcademicYear::query()
            ->has('waves')
            ->withCount('waves')
            ->orderByDesc('is_active')
            ->get();

        foreach ($years as $year) {
            $waves = $year->waves()->get();
            $count = $year->is_active ? 30 : 15;

            SpmbRegistration::factory()
                ->count($count)
                ->sequence(function () use ($year, $waves, $paths): array {
                    $wave = $waves->random();

                    return [
                        'institution_id' => $wave->institution_id,
                        'academic_year_id' => $year->id,
                        'registration_wave_id' => $wave->id,
                        'admission_path_id' => fake()->randomElement($paths),
                    ];
                })
                ->create();
        }
    }
}
