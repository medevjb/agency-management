<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $value = fake()->randomFloat(2, 100, 5000);

        return [
            'order_id' => 'ORD-'.fake()->unique()->numberBetween(10000, 99999),
            'name' => fake()->unique()->company(),
            'description' => fake()->sentence(),
            'status' => 'in-progress',
            'progress' => 0,
            'priority' => 'medium',
            'value' => $value,
            'actual_revenue' => round($value * 0.80, 2),
            'start_date' => now()->subDays(3),
            'end_date' => now()->addDays(7),
            'created_by' => User::factory(),
        ];
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'progress' => 100,
            'delivered_at' => now(),
        ]);
    }
}
