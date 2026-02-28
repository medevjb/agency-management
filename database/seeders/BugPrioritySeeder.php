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
                'name' => 'High',
                'slug' => 'high',
                'color' => '#EF4444', // Red
                'level' => 2,
            ],
            [
                'name' => 'Medium',
                'slug' => 'medium',
                'color' => '#F59E0B', // Amber/Orange
                'level' => 1,
            ],
            [
                'name' => 'Low',
                'slug' => 'low',
                'color' => '#10B981', // Green
                'level' => 0,
            ]
        ];

        foreach ($priorities as $priority) {
            BugPriority::updateOrCreate(
                ['slug' => $priority['slug']],
                $priority
            );
        }
    }
}
