<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroupMember>
 */
class GroupMemberFactory extends Factory
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
            'full_name' => fake()->name(),
            'nik' => fake()->optional()->numerify('################'),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_date' => fake()->optional()->date(),
            'birth_place' => fake()->optional()->city(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'address' => fake()->optional()->address(),
            'parent_name' => fake()->optional()->name(),
            'parent_phone' => fake()->optional()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'status' => 'pending',
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
