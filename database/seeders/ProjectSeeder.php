<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devTeam = Team::where('name', 'Development Team')->first();
        $designTeam = Team::where('name', 'Design Team')->first();
        $laravelStack = TechStack::where('name', 'Laravel')->first();
        $reactStack = TechStack::where('name', 'React')->first();
        $adminUser = User::where('email', 'admin@test.com')->first();

        $projects = [
            [
                'name' => 'E-commerce Platform',
                'description' => 'A full-featured e-commerce platform with payment integration, inventory management, and order tracking.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-30',
                'budget' => 50000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Mobile Banking App',
                'description' => 'Secure mobile banking application with real-time transactions and biometric authentication.',
                'status' => 'planning',
                'priority' => 'urgent',
                'start_date' => '2024-02-01',
                'end_date' => '2024-08-31',
                'budget' => 75000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'CRM System',
                'description' => 'Customer Relationship Management system with lead tracking, analytics, and automated workflows.',
                'status' => 'completed',
                'priority' => 'medium',
                'start_date' => '2023-09-01',
                'end_date' => '2024-01-15',
                'budget' => 35000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Healthcare Portal',
                'description' => 'Patient portal for appointment scheduling, medical records access, and telemedicine consultations.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2024-01-01',
                'end_date' => '2024-07-31',
                'budget' => 60000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'UI/UX Design System',
                'description' => 'Comprehensive design system with components, patterns, and guidelines for consistent UI across products.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'start_date' => '2024-01-10',
                'end_date' => '2024-04-30',
                'budget' => 25000.00,
                'team_id' => $designTeam->id,
                'tech_stack_id' => null,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Dashboard Analytics',
                'description' => 'Real-time analytics dashboard with interactive charts, reports, and business intelligence features.',
                'status' => 'on-hold',
                'priority' => 'low',
                'start_date' => '2023-11-01',
                'end_date' => '2024-03-31',
                'budget' => 20000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Social Media Platform',
                'description' => 'Social networking platform with real-time messaging, content sharing, and community features.',
                'status' => 'planning',
                'priority' => 'medium',
                'start_date' => '2024-03-01',
                'end_date' => '2024-12-31',
                'budget' => 100000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Learning Management System',
                'description' => 'Online learning platform with course management, video streaming, and progress tracking.',
                'status' => 'completed',
                'priority' => 'medium',
                'start_date' => '2023-08-01',
                'end_date' => '2023-12-15',
                'budget' => 40000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'created_by' => $adminUser->id,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->command->info('✅ Sample projects created successfully!');
    }
}