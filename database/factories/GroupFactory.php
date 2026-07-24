<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['Group A', 'Group B', 'Dolphins', 'Sharks', 'Turtles']).' '.fake()->numberBetween(1, 9);

        return [
            'institution_id' => Institution::factory(),
            'teacher_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'level' => fake()->randomElement(['Beginner', 'Intermediate', 'Advanced']),
            'days' => fake()->randomElements(['mon', 'tue', 'wed', 'thu', 'fri', 'sat'], fake()->numberBetween(1, 2)),
            'start_time' => fake()->randomElement(['15:00', '16:00', '08:00']),
            'end_time' => fake()->randomElement(['16:30', '17:30', '10:00']),
            'location' => fake()->optional()->city(),
            'capacity' => fake()->optional()->numberBetween(5, 20),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
