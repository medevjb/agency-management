<?php

namespace Database\Seeders;

use App\Models\BugCategory;
use Illuminate\Database\Seeder;

class BugCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'UI/UX',
                'slug' => 'ui-ux',
                'description' => 'Issues related to user interface and user experience',
            ],
            [
                'name' => 'Backend',
                'slug' => 'backend',
                'description' => 'Server-side logic, database, and API issues',
            ],
            [
                'name' => 'Frontend',
                'slug' => 'frontend',
                'description' => 'Client-side functionality and JavaScript issues',
            ],
            [
                'name' => 'Database',
                'slug' => 'database',
                'description' => 'Database structure, queries, and data integrity issues',
            ],
            [
                'name' => 'API',
                'slug' => 'api',
                'description' => 'API endpoints, responses, and integration issues',
            ],
            [
                'name' => 'Performance',
                'slug' => 'performance',
                'description' => 'Slow loading, high resource usage, optimization needs',
            ],
            [
                'name' => 'Security',
                'slug' => 'security',
                'description' => 'Security vulnerabilities and access control issues',
            ],
            [
                'name' => 'Feature Request',
                'slug' => 'feature-request',
                'description' => 'New feature suggestions and enhancements',
            ],
            [
                'name' => 'Documentation',
                'slug' => 'documentation',
                'description' => 'Documentation errors and improvements',
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Issues that do not fit into other categories',
            ],
        ];

        foreach ($categories as $category) {
            BugCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
