<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sample>
 */
class SampleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sample_code' => 'SAMPLE-' . strtoupper($this->faker->unique()->bothify('??####')),
            'patient_name' => $this->faker->name(),
        ];
    }
}
