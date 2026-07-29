<?php

namespace Database\Factories;

use App\Models\CoursePayment;
use App\Models\GroupMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CoursePayment>
 */
class CoursePaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_member_id' => GroupMember::factory(),
            'group_session_id' => null,
            'amount' => fake()->randomElement([25000, 50000, 75000]),
            'status' => 'unpaid',
            'method' => null,
            'paid_at' => null,
            'notes' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state([
            'status' => 'paid',
            'method' => 'cash',
            'paid_at' => now(),
        ]);
    }

    public function waived(): static
    {
        return $this->state(['status' => 'waived']);
    }
}
