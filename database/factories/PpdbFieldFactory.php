<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\PpdbField;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PpdbField>
 */
class PpdbFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = fake()->unique()->words(2, true);

        return [
            'institution_id' => Institution::factory(),
            'key' => Str::slug($label, '_'),
            'label' => Str::title($label),
            'type' => 'text',
            'options' => null,
            'placeholder' => null,
            'help_text' => null,
            'is_required' => false,
            'width' => 'full',
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }

    public function required(): static
    {
        return $this->state(['is_required' => true]);
    }

    /**
     * @param  array<int, string>  $values
     */
    public function select(array $values = ['A', 'B', 'C']): static
    {
        return $this->state([
            'type' => 'select',
            'options' => array_map(fn (string $value): array => ['value' => $value], $values),
        ]);
    }
}
