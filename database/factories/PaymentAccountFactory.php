<?php

namespace Database\Factories;

use App\Models\PaymentAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentAccount>
 */
class PaymentAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'bank',
            'label' => null,
            'bank_name' => fake()->randomElement(['BCA', 'BNI', 'BRI', 'Mandiri']),
            'account_number' => (string) fake()->numerify('##########'),
            'account_holder' => fake()->name(),
            'qris_image' => null,
            'instructions' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function qris(): static
    {
        return $this->state([
            'type' => 'qris',
            'label' => 'QRIS',
            'bank_name' => null,
            'account_number' => null,
            'account_holder' => null,
            'qris_image' => 'payment-accounts/qris.png',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
