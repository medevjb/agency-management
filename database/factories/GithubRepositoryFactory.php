<?php

namespace Database\Factories;

use App\Models\GithubAccount;
use App\Models\GithubRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GithubRepository>
 */
class GithubRepositoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->slug(2);
        $owner = $this->faker->userName();

        return [
            'github_account_id' => GithubAccount::factory(),
            'full_name' => "{$owner}/{$name}",
            'name' => $name,
            'html_url' => "https://github.com/{$owner}/{$name}",
            'default_branch' => 'main',
        ];
    }
}
