<?php

namespace Database\Seeders;

use App\Models\BugPriority;
use Illuminate\Database\Seeder;

class BugPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            [
                'name' => 'Low',
                'slug' => 'low',
                'color' => '#10B981', // Green
                'level' => 0,
            ],
            [
                'name' => 'Medium',
                'slug' => 'medium',
                'color' => '#F59E0B', // Amber/Orange
                'level' => 1,
            ],
            [
                'name' => 'High',
                'slug' => 'high',
                'color' => '#EF4444', // Red
                'level' => 2,
            ],
            [
                'name' => 'Critical',
                'slug' => 'critical',
                'color' => '#7C3AED', // Purple
                'level' => 3,
            ],
        ];

        foreach ($priorities as $priority) {
            BugPriority::updateOrCreate(
                ['slug' => $priority['slug']],
                $priority
            );
        }
    }
}
