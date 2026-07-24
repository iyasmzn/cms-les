<?php

namespace Database\Factories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $isAnonymous = fake()->boolean(20);
        $status = fake()->randomElement(['pending', 'confirmed', 'rejected']);

        return [
            'name' => $isAnonymous ? 'Anonim' : fake()->name(),
            'email' => fake()->optional(0.6)->safeEmail(),
            'phone' => fake()->optional(0.7)->numerify('08##########'),
            'is_anonymous' => $isAnonymous,
            'amount' => fake()->randomElement([50000, 100000, 150000, 200000, 250000, 500000, 1000000]),
            'payment_method' => fake()->randomElement(['transfer_bank', 'qris', 'tunai', 'lainnya']),
            'message' => fake()->optional(0.6)->sentence(10),
            'status' => $status,
            'confirmed_at' => $status === 'confirmed' ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'notes' => fake()->optional(0.3)->sentence(8),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending', 'confirmed_at' => null]);
    }

    public function confirmed(): static
    {
        return $this->state(['status' => 'confirmed', 'confirmed_at' => now()]);
    }
}
