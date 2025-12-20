<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create teams and tech stacks
        $devTeam = Team::firstOrCreate(['name' => 'Development Team'], [
            'description' => 'Core software development team responsible for building and maintaining applications.'
        ]);

        $marketingTeam = Team::firstOrCreate(['name' => 'Marketing Team'], [
            'description' => 'Marketing and growth team responsible for product promotion and user acquisition.'
        ]);

        $designTeam = Team::firstOrCreate(['name' => 'Design Team'], [
            'description' => 'UI/UX design team responsible for user experience and visual design.'
        ]);

        $laravelStack = TechStack::firstOrCreate(['name' => 'Laravel'], [
            'description' => 'PHP framework for web application development with elegant syntax and powerful features.'
        ]);

        $reactStack = TechStack::firstOrCreate(['name' => 'React'], [
            'description' => 'JavaScript library for building user interfaces with component-based architecture.'
        ]);

        $vueStack = TechStack::firstOrCreate(['name' => 'Vue.js'], [
            'description' => 'Progressive JavaScript framework for building interactive web interfaces.'
        ]);

        $pythonStack = TechStack::firstOrCreate(['name' => 'Python'], [
            'description' => 'High-level programming language for data analysis, AI, and backend development.'
        ]);

        // Get existing users
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UsersWithRolesSeeder first.');
            return;
        }

        $admin = $users->firstWhere('email', 'admin@test.com');
        $teamLead = $users->firstWhere('email', 'teamlead1@test.com');
        $designLead = $users->firstWhere('email', 'teamlead2@test.com');

        // Clear existing demo projects to avoid duplicates
        Project::whereNotNull('id')->delete();

        // Demo Projects
        $projects = [
            [
                'name' => 'E-Commerce Platform',
                'description' => 'A comprehensive e-commerce solution with product management, secure payment processing, inventory tracking, and customer relationship management. Features include real-time inventory updates, multi-vendor support, advanced analytics dashboard, and mobile-responsive design.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2024-01-15',
                'end_date' => '2024-08-30',
                'budget' => 85000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'created_by' => $admin->id,
                'tasks' => [
                    ['title' => 'Setup Laravel Project Architecture', 'description' => 'Initialize Laravel project with proper MVC structure and configure basic settings', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 8, 'due_date' => '2024-01-20'],
                    ['title' => 'Database Design & Models', 'description' => 'Create database schema for products, users, orders, and categories', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 12, 'due_date' => '2024-01-25'],
                    ['title' => 'User Authentication System', 'description' => 'Implement secure user registration, login, and password reset functionality', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 10, 'due_date' => '2024-02-01'],
                    ['title' => 'Product Management Module', 'description' => 'Create CRUD operations for product management with image upload', 'status' => 'in-progress', 'priority' => 'high', 'estimated_hours' => 16, 'due_date' => '2024-02-15'],
                    ['title' => 'Shopping Cart Implementation', 'description' => 'Build shopping cart functionality with session-based storage', 'status' => 'in-progress', 'priority' => 'medium', 'estimated_hours' => 14, 'due_date' => '2024-02-20'],
                    ['title' => 'Payment Gateway Integration', 'description' => 'Integrate Stripe and PayPal for secure payment processing', 'status' => 'todo', 'priority' => 'high', 'estimated_hours' => 20, 'due_date' => '2024-03-01'],
                    ['title' => 'Admin Dashboard', 'description' => 'Create comprehensive admin panel for order and inventory management', 'status' => 'todo', 'priority' => 'medium', 'estimated_hours' => 18, 'due_date' => '2024-03-15'],
                ]
            ],
            [
                'name' => 'Mobile Banking Application',
                'description' => 'A secure and feature-rich mobile banking application with real-time transactions, biometric authentication, card management, bill payments, peer-to-peer transfers, and advanced security features including two-factor authentication and fraud detection.',
                'status' => 'planning',
                'priority' => 'urgent',
                'start_date' => '2024-02-01',
                'end_date' => '2024-12-15',
                'budget' => 120000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'created_by' => $admin->id,
                'tasks' => [
                    ['title' => 'Security Architecture Planning', 'description' => 'Define security requirements and architecture for mobile banking app', 'status' => 'completed', 'priority' => 'urgent', 'estimated_hours' => 16, 'due_date' => '2024-02-15'],
                    ['title' => 'Regulatory Compliance Research', 'description' => 'Research banking regulations and compliance requirements', 'status' => 'completed', 'priority' => 'urgent', 'estimated_hours' => 12, 'due_date' => '2024-02-10'],
                    ['title' => 'Native App Development Setup', 'description' => 'Setup React Native development environment and project structure', 'status' => 'in-progress', 'priority' => 'high', 'estimated_hours' => 8, 'due_date' => '2024-02-25'],
                    ['title' => 'Biometric Authentication Module', 'description' => 'Implement fingerprint and facial recognition authentication', 'status' => 'todo', 'priority' => 'urgent', 'estimated_hours' => 24, 'due_date' => '2024-03-15'],
                    ['title' => 'Transaction Processing Engine', 'description' => 'Build secure transaction processing with encryption', 'status' => 'todo', 'priority' => 'urgent', 'estimated_hours' => 32, 'due_date' => '2024-04-01'],
                    ['title' => 'API Integration with Banking Systems', 'description' => 'Integrate with external banking APIs for real-time data', 'status' => 'todo', 'priority' => 'high', 'estimated_hours' => 40, 'due_date' => '2024-05-01'],
                ]
            ],
            [
                'name' => 'Learning Management System',
                'description' => 'An interactive online learning platform with video streaming, course management, progress tracking, live classes, assignments, quizzes, certifications, gamification elements, and collaborative learning tools for educational institutions.',
                'status' => 'completed',
                'priority' => 'medium',
                'start_date' => '2023-09-01',
                'end_date' => '2024-02-15',
                'budget' => 65000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $vueStack->id,
                'created_by' => $admin->id,
                'tasks' => [
                    ['title' => 'Course Management System', 'description' => 'Build system for creating and managing courses with content', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 20, 'due_date' => '2023-10-01'],
                    ['title' => 'Video Streaming Platform', 'description' => 'Implement video upload and streaming with adaptive bitrate', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 24, 'due_date' => '2023-10-15'],
                    ['title' => 'Student Dashboard', 'description' => 'Create personalized dashboard for students with course progress', 'status' => 'completed', 'priority' => 'medium', 'estimated_hours' => 16, 'due_date' => '2023-11-01'],
                    ['title' => 'Quiz and Assessment Engine', 'description' => 'Build comprehensive quiz system with multiple question types', 'status' => 'completed', 'priority' => 'medium', 'estimated_hours' => 18, 'due_date' => '2023-11-15'],
                    ['title' => 'Certificate Generation System', 'description' => 'Automated certificate generation with customizable templates', 'status' => 'completed', 'priority' => 'low', 'estimated_hours' => 12, 'due_date' => '2023-12-01'],
                ]
            ],
            [
                'name' => 'Healthcare Management System',
                'description' => 'A comprehensive healthcare management system for hospitals and clinics with patient records management, appointment scheduling, telemedicine consultations, medical billing, inventory management, and detailed analytics dashboard.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2024-01-01',
                'end_date' => '2024-09-30',
                'budget' => 95000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'created_by' => $admin->id,
                'tasks' => [
                    ['title' => 'Patient Records Management', 'description' => 'Create EMR system with comprehensive patient data storage', 'status' => 'completed', 'priority' => 'urgent', 'estimated_hours' => 32, 'due_date' => '2024-01-31'],
                    ['title' => 'Appointment Scheduling System', 'description' => 'Build appointment booking system with calendar integration', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 20, 'due_date' => '2024-02-15'],
                    ['title' => 'Telemedicine Platform', 'description' => 'Implement video consultation platform with screen sharing', 'status' => 'in-progress', 'priority' => 'high', 'estimated_hours' => 36, 'due_date' => '2024-03-15'],
                    ['title' => 'Medical Billing System', 'description' => 'Create insurance billing and payment processing system', 'status' => 'in-progress', 'priority' => 'medium', 'estimated_hours' => 28, 'due_date' => '2024-04-01'],
                    ['title' => 'Pharmacy Inventory Management', 'description' => 'Build inventory tracking for medicines and medical supplies', 'status' => 'todo', 'priority' => 'medium', 'estimated_hours' => 24, 'due_date' => '2024-05-01'],
                    ['title' => 'Analytics Dashboard', 'description' => 'Create healthcare analytics and reporting dashboard', 'status' => 'todo', 'priority' => 'low', 'estimated_hours' => 20, 'due_date' => '2024-06-01'],
                ]
            ],
            [
                'name' => 'Brand Redesign Campaign',
                'description' => 'A comprehensive brand redesign campaign for a major tech company including new logo design, brand guidelines, marketing materials, social media strategy, website redesign, and launch campaign with targeted advertising.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'start_date' => '2024-02-15',
                'end_date' => '2024-06-30',
                'budget' => 45000.00,
                'team_id' => $marketingTeam->id,
                'tech_stack_id' => null,
                'created_by' => $admin->id,
                'tasks' => [
                    ['title' => 'Market Research & Analysis', 'description' => 'Conduct comprehensive market research and competitor analysis', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 40, 'due_date' => '2024-03-01'],
                    ['title' => 'Brand Guidelines Development', 'description' => 'Create comprehensive brand guidelines and style guide', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 24, 'due_date' => '2024-03-15'],
                    ['title' => 'Logo Design Concepts', 'description' => 'Design and develop multiple logo concepts and variations', 'status' => 'completed', 'priority' => 'high', 'estimated_hours' => 20, 'due_date' => '2024-03-20'],
                    ['title' => 'Marketing Materials Creation', 'description' => 'Design brochures, business cards, and marketing collateral', 'status' => 'in-progress', 'priority' => 'medium', 'estimated_hours' => 32, 'due_date' => '2024-04-15'],
                    ['title' => 'Social Media Strategy', 'description' => 'Develop comprehensive social media marketing strategy', 'status' => 'in-progress', 'priority' => 'medium', 'estimated_hours' => 28, 'due_date' => '2024-04-30'],
                    ['title' => 'Launch Campaign Planning', 'description' => 'Plan and coordinate brand launch campaign activities', 'status' => 'todo', 'priority' => 'high', 'estimated_hours' => 36, 'due_date' => '2024-05-15'],
                ]
            ]
        ];

        // Create projects and their tasks
        foreach ($projects as $projectData) {
            $tasks = $projectData['tasks'];
            unset($projectData['tasks']);

            $project = Project::create($projectData);

            // Create tasks for each project
            foreach ($tasks as $taskData) {
                $assignedUser = $users->random(); // Randomly assign tasks to users

                Task::create([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'status' => $taskData['status'],
                    'priority' => $taskData['priority'],
                    'due_date' => $taskData['due_date'],
                    'estimated_hours' => $taskData['estimated_hours'],
                    'actual_hours' => $taskData['status'] === 'completed' ? rand($taskData['estimated_hours'] - 2, $taskData['estimated_hours'] + 2) : null,
                    'project_id' => $project->id,
                    'assigned_to' => $assignedUser->id,
                    'created_by' => $admin->id,
                ]);
            }

            // Assign some team members to projects
            $projectMembers = $users->random(rand(2, 4));
            foreach ($projectMembers as $member) {
                $project->members()->attach($member->id, [
                    'role' => rand(0, 2) === 0 ? 'lead' : 'member',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Demo projects and tasks created successfully!');
        $this->command->info('📊 Created ' . count($projects) . ' comprehensive demo projects');
        $this->command->info('👥 Assigned team members to each project');
    }
}
