<?php

namespace Database\Factories;

use App\Models\TechStack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TechStack>
 */
class TechStackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
        ];
    }
}
