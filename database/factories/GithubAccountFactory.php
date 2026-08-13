<?php

namespace Database\Factories;

use App\Models\GithubAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GithubAccount>
 */
class GithubAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->company().' GitHub',
            'username' => $this->faker->unique()->userName(),
            'token' => 'ghp_'.$this->faker->regexify('[A-Za-z0-9]{36}'),
            'avatar_url' => 'https://avatars.githubusercontent.com/u/'.$this->faker->numberBetween(1, 999999),
            'created_by' => null,
        ];
    }
}
