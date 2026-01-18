<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::take(8)->get();
        $users = User::take(10)->get();

        if ($orders->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No orders or users found. Skipping demo tasks creation.');

            return;
        }

        $demoTasks = [
            [
                'title' => 'Design homepage mockup',
                'description' => 'Create initial wireframes and high-fidelity mockups for the new homepage design. Include mobile responsive layouts.',
                'status' => 'in-progress',
                'priority' => 'high',
                'estimated_hours' => 8,
                'actual_hours' => 4,
            ],
            [
                'title' => 'Implement user authentication',
                'description' => 'Set up Laravel Breeze with custom authentication. Include login, registration, password reset, and email verification.',
                'status' => 'pending',
                'priority' => 'urgent',
                'estimated_hours' => 12,
            ],
            [
                'title' => 'Database optimization',
                'description' => 'Review and optimize database queries. Add necessary indexes and refactor N+1 queries.',
                'status' => 'pending',
                'priority' => 'medium',
                'estimated_hours' => 6,
            ],
            [
                'title' => 'Write API documentation',
                'description' => 'Document all REST API endpoints using OpenAPI/Swagger specification. Include request/response examples.',
                'status' => 'on-hold',
                'priority' => 'low',
                'estimated_hours' => 10,
            ],
            [
                'title' => 'Setup CI/CD pipeline',
                'description' => 'Configure GitHub Actions for automated testing and deployment. Set up staging and production environments.',
                'status' => 'completed',
                'priority' => 'high',
                'estimated_hours' => 16,
                'actual_hours' => 14,
            ],
            [
                'title' => 'Fix payment integration bug',
                'description' => 'Investigate and fix the issue with Stripe payment processing. Error occurs during webhook handling.',
                'status' => 'in-progress',
                'priority' => 'urgent',
                'estimated_hours' => 4,
            ],
            [
                'title' => 'Create admin dashboard',
                'description' => 'Build comprehensive admin dashboard with analytics, user management, and system monitoring.',
                'status' => 'pending',
                'priority' => 'high',
                'estimated_hours' => 20,
            ],
            [
                'title' => 'Implement search functionality',
                'description' => 'Add full-text search across all major entities using Laravel Scout or database search.',
                'status' => 'pending',
                'priority' => 'medium',
                'estimated_hours' => 8,
            ],
            [
                'title' => 'Mobile app UI redesign',
                'description' => 'Update mobile app interface to match new brand guidelines. Improve user experience and navigation.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'estimated_hours' => 24,
                'actual_hours' => 12,
            ],
            [
                'title' => 'Security audit',
                'description' => 'Conduct comprehensive security review. Check for XSS, SQL injection, and other vulnerabilities.',
                'status' => 'pending',
                'priority' => 'high',
                'estimated_hours' => 16,
            ],
            [
                'title' => 'Performance testing',
                'description' => 'Run load testing and performance benchmarks. Optimize slow endpoints and database queries.',
                'status' => 'on-hold',
                'priority' => 'medium',
                'estimated_hours' => 12,
            ],
            [
                'title' => 'User feedback integration',
                'description' => 'Implement in-app feedback system. Allow users to report bugs and suggest features.',
                'status' => 'pending',
                'priority' => 'low',
                'estimated_hours' => 6,
            ],
            [
                'title' => 'Email notification system',
                'description' => 'Build email notification system for important events. Use Laravel Mail and queue system.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'estimated_hours' => 10,
                'actual_hours' => 6,
            ],
            [
                'title' => 'Refactor legacy code',
                'description' => 'Clean up and refactor old codebase. Apply SOLID principles and improve code quality.',
                'status' => 'pending',
                'priority' => 'low',
                'estimated_hours' => 40,
            ],
            [
                'title' => 'Implement file upload feature',
                'description' => 'Add file upload functionality with drag-and-drop. Support multiple file types and size validation.',
                'status' => 'pending',
                'priority' => 'high',
                'estimated_hours' => 14,
            ],
            [
                'title' => 'Create data export feature',
                'description' => 'Build data export functionality. Support CSV, Excel, and PDF formats.',
                'status' => 'completed',
                'priority' => 'medium',
                'estimated_hours' => 12,
                'actual_hours' => 11,
            ],
            [
                'title' => 'Setup monitoring and logging',
                'description' => 'Implement application monitoring with Sentry. Add comprehensive logging for debugging.',
                'status' => 'in-progress',
                'priority' => 'high',
                'estimated_hours' => 8,
                'actual_hours' => 5,
            ],
            [
                'title' => 'Create onboarding flow',
                'description' => 'Design and implement user onboarding experience. Include tutorials and feature highlights.',
                'status' => 'pending',
                'priority' => 'medium',
                'estimated_hours' => 18,
            ],
            [
                'title' => 'Implement role-based access',
                'description' => 'Setup role-based access control using Laravel Gates and Policies. Define user permissions.',
                'status' => 'completed',
                'priority' => 'urgent',
                'estimated_hours' => 14,
                'actual_hours' => 13,
            ],
            [
                'title' => 'Build real-time notifications',
                'description' => 'Implement real-time notification system using Laravel Echo and Pusher or WebSockets.',
                'status' => 'pending',
                'priority' => 'medium',
                'estimated_hours' => 22,
            ],
        ];

        $createdBy = $users->first()->id;

        foreach ($demoTasks as $index => $taskData) {
            $order = $orders->get($index % $orders->count());
            $assignedTo = $users->random();

            $task = Task::create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status'],
                'priority' => $taskData['priority'],
                'estimated_hours' => $taskData['estimated_hours'],
                'actual_hours' => $taskData['actual_hours'] ?? null,
                'order_id' => $order->id,
                'assigned_to' => $assignedTo->id,
                'created_by' => $createdBy,
                'due_date' => now()->addDays(rand(1, 45)),
            ]);

            // Set completed_at for completed tasks
            if ($taskData['status'] === 'completed') {
                $task->update([
                    'completed_at' => now()->subDays(rand(1, 14)),
                ]);
            }

            $this->command->info("Created task: {$task->title}");
        }

        $this->command->info('Successfully created 20 demo tasks!');
    }
}
