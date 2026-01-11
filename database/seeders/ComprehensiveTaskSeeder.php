<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComprehensiveTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing tasks
        Task::query()->delete();

        $orders = Order::all();
        $users = User::where('is_active', true)->get();

        if ($orders->isEmpty()) {
            $this->command->error('No orders found. Please run order seeder first.');

            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run user seeder first.');

            return;
        }

        $admin = $users->firstWhere('email', 'admin@test.com');

        $tasks = [];

        // Generate tasks for each order
        foreach ($orders as $order) {
            $taskCount = rand(5, 12);

            for ($i = 1; $i <= $taskCount; $i++) {
                $status = match (true) {
                    $order->status === 'completed' && $i <= $taskCount * 0.8 => 'completed',
                    $order->status === 'completed' => 'completed',
                    $order->status === 'in-progress' && $i <= $taskCount * 0.5 => 'completed',
                    $order->status === 'in-progress' && $i <= $taskCount * 0.7 => 'in-progress',
                    $order->status === 'planning' => 'pending',
                    $i <= 3 => 'in-progress',
                    default => 'pending',
                };

                $priority = match (rand(1, 10)) {
                    1, 2 => 'urgent',
                    3, 4, 5 => 'high',
                    6, 7, 8 => 'medium',
                    default => 'low',
                };

                $assignedUser = $users->random();

                $task = [
                    'title' => $this->generateTaskTitle($order->name, $i, $priority),
                    'description' => $this->generateTaskDescription($order->name, $priority),
                    'status' => $status,
                    'priority' => $priority,
                    'order_id' => $order->id,
                    'assigned_to' => $assignedUser->id,
                    'estimated_hours' => rand(2, 40),
                    'due_date' => $this->generateDueDate($order, $status),
                    'created_by' => $admin->id,
                ];

                // Add actual hours for completed tasks
                if ($status === 'completed') {
                    $task['actual_hours'] = $task['estimated_hours'] + rand(-3, 5);
                }

                $tasks[] = $task;
            }
        }

        // Create all tasks
        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        $this->command->info('✅ Tasks created successfully!');
        $this->command->info('✅ Created '.count($tasks).' tasks across '.$orders->count().' orders');
    }

    private function generateTaskTitle($orderName, $index, $priority): string
    {
        $prefixes = [
            'Design', 'Implement', 'Develop', 'Create', 'Build', 'Setup', 'Configure',
            'Test', 'Review', 'Optimize', 'Refactor', 'Deploy', 'Document', 'Analyze',
        ];

        $suffixes = [
            'System', 'Module', 'Feature', 'Component', 'Interface', 'API', 'Database Schema',
            'User Interface', 'Backend Logic', 'Authentication', 'Integration', 'Tests',
            'Documentation', 'Performance', 'Security', 'Deployment Pipeline',
        ];

        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = $suffixes[array_rand($suffixes)];

        if ($priority === 'urgent') {
            return "[$priority] $prefix $suffix for $orderName";
        }

        return "$prefix $suffix - Phase $index";
    }

    private function generateTaskDescription($orderName, $priority): string
    {
        $descriptions = [
            "Complete implementation of core functionality for $orderName with focus on performance and scalability.",
            "Design and develop critical components for $orderName following best practices and coding standards.",
            "Create comprehensive test suite for $orderName including unit tests and integration tests.",
            "Review and optimize existing codebase for $orderName to improve performance and maintainability.",
            "Implement security measures and vulnerability fixes for $orderName following OWASP guidelines.",
            "Develop user interface components for $orderName with responsive design and accessibility features.",
            "Set up CI/CD pipeline and deployment automation for $orderName to streamline development workflow.",
            "Write technical documentation and API references for $orderName development team.",
            "Conduct code review and quality assurance testing for $orderName features.",
            "Analyze system requirements and architect solution for $orderName scalability requirements.",
        ];

        if ($priority === 'urgent') {
            return 'CRITICAL: Immediate attention required - '.$descriptions[array_rand($descriptions)];
        }

        return $descriptions[array_rand($descriptions)];
    }

    private function generateDueDate($order, $status)
    {
        $baseDate = now();

        if ($order->status === 'completed' || $status === 'completed') {
            // Completed tasks have past due dates
            return $baseDate->subDays(rand(10, 60))->format('Y-m-d');
        }

        if ($order->status === 'in-progress') {
            // In-progress tasks have near future due dates
            return $baseDate->addDays(rand(5, 30))->format('Y-m-d');
        }

        if ($order->status === 'planning') {
            // Planning tasks have future due dates
            return $baseDate->addDays(rand(15, 60))->format('Y-m-d');
        }

        return $baseDate->addDays(rand(7, 45))->format('Y-m-d');
    }
}
