<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            ProfilePermissionSeeder::class,
            ComprehensiveUserSeeder::class,
            AssignRolesAndPermissionsSeeder::class,
            ComprehensiveProfileSeeder::class,
            ComprehensiveTeamSeeder::class,
            ComprehensiveTechStackSeeder::class,
            OrderSeeder::class,
            ComprehensiveTaskSeeder::class,
            ComprehensivePortfolioSeeder::class,
            BugCategorySeeder::class,
            BugPrioritySeeder::class,
        ]);
    }
}
