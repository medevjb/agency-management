<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        // Create profiles for some users
        foreach ($users as $user) {
            // 70% chance to create a profile for each user
            if (fake()->boolean(70)) {
                Profile::factory()->withUser($user->id)->create();
            }
        }

        // Create some additional profiles without users (freelancer accounts)
        Profile::factory()->count(10)->create();
    }
}
