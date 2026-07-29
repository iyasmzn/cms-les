<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroupSession>
 */
class GroupSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'date' => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'start_time' => '16:00',
            'end_time' => '17:30',
            'location' => fake()->optional()->city(),
            'topic' => fake()->optional()->sentence(3),
            'fee' => fake()->optional()->randomElement([25000, 50000, 75000]),
            'status' => 'scheduled',
        ];
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }
}
