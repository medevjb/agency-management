<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skills = [
            'PHP,Laravel,MySQL', 'JavaScript,Vue.js,React', 'Python,Django,Flask',
            'Java,Spring,MySQL', 'Node.js,Express,MongoDB', 'iOS,Swift,SwiftUI',
            'Android,Kotlin,Java', 'React Native,Flutter', 'UI/UX Design,Figma,Adobe XD',
            'DevOps,Docker,Kubernetes,AWS', 'Data Science,Python,Machine Learning',
            'WordPress,Elementor,PHP', 'Shopify,Liquid,React', 'HTML,CSS,JavaScript,jQuery',
        ];

        return [
            'user_id' => null,
            'profile_name' => fake()->name(),
            'username' => fake()->unique()->regexify('[a-zA-Z0-9_]{5,15}'),
            'platform' => fake()->randomElement(['fiverr', 'upwork']),
            'avatar' => null,
            'bio' => fake()->paragraphs(2, true),
            'skills' => fake()->randomElement($skills),
            'hourly_rate' => '$'.fake()->numberBetween(25, 150).'/hr',
            'status' => fake()->randomElement(['active', 'inactive', 'busy', 'away']),
            'is_verified' => fake()->boolean(70),
            'is_featured' => fake()->boolean(20),
            'rating' => fake()->numberBetween(0, 5),
            'total_jobs' => fake()->numberBetween(0, 100),
        ];
    }

    public function withUser(mixed $userId): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}
