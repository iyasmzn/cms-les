<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Institution;
use App\Models\RegistrationWave;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<RegistrationWave>
 */
class RegistrationWaveFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::instance(fake()->dateTimeBetween('-1 month', '+1 month'));
        $end = $start->copy()->addDays(fake()->numberBetween(14, 45));

        return [
            'academic_year_id' => AcademicYear::factory(),
            'institution_id' => Institution::factory(),
            'name' => 'Gelombang '.fake()->numberBetween(1, 3),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'selection_date' => $end->copy()->addDays(5)->toDateString(),
            'announcement_date' => $end->copy()->addDays(12)->toDateString(),
            'is_active' => true,
        ];
    }

    public function open(): static
    {
        return $this->state([
            'start_date' => Carbon::today()->subWeek()->toDateString(),
            'end_date' => Carbon::today()->addWeek()->toDateString(),
            'is_active' => true,
        ]);
    }
}
