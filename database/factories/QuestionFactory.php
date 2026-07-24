<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $answered = fake()->boolean(60);

        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->optional()->safeEmail(),
            'is_anonymous' => false,
            'question' => fake()->paragraph(),
            'answer' => $answered ? fake()->paragraph() : null,
            'is_answered' => $answered,
            'is_published' => fake()->boolean(80),
            'answered_at' => $answered ? fake()->dateTimeBetween('-6 months', 'now') : null,
        ];
    }

    public function answered(): static
    {
        return $this->state([
            'is_answered' => true,
            'answer' => fake()->paragraph(),
            'answered_at' => now(),
        ]);
    }

    public function unanswered(): static
    {
        return $this->state([
            'is_answered' => false,
            'answer' => null,
            'answered_at' => null,
        ]);
    }

    /** The asker chose to have their name hidden publicly. */
    public function anonymous(): static
    {
        return $this->state(['is_anonymous' => true]);
    }
}
