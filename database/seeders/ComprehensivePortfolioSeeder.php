<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComprehensivePortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing portfolios
        Portfolio::query()->forceDelete();

        $users = User::where('is_active', true)->get();
        $techStacks = TechStack::all();

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run user seeder first.');

            return;
        }

        if ($techStacks->isEmpty()) {
            $this->command->error('No tech stacks found. Please run tech stack seeder first.');

            return;
        }

        $portfolios = [
            [
                'title' => 'E-Commerce Platform',
                'description' => 'Full-featured e-commerce platform with product management, shopping cart, payment integration, order tracking, and admin dashboard. Built with modern technologies and best practices.',
                'project_url' => 'https://demo-ecommerce.example.com',
                'github_url' => 'https://github.com/demo/ecommerce-platform',
                'status' => 'published',
                'is_featured' => true,
                'tech_stack' => 'Laravel',
            ],
            [
                'title' => 'Real Estate Mobile App',
                'description' => 'Cross-platform mobile application for real estate listings with advanced search filters, virtual tours, map integration, and agent contact features.',
                'project_url' => 'https://apps.apple.com/app/realestate',
                'github_url' => null,
                'status' => 'published',
                'is_featured' => true,
                'tech_stack' => 'React Native',
            ],
            [
                'title' => 'Healthcare Management System',
                'description' => 'Comprehensive healthcare management system with patient records, appointment scheduling, telemedicine consultations, prescription management, and billing.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/healthcare-system',
                'status' => 'published',
                'is_featured' => true,
                'tech_stack' => 'Python',
            ],
            [
                'title' => 'Social Media Dashboard',
                'description' => 'Analytics dashboard for social media management with real-time metrics, post scheduling, engagement tracking, and multi-platform integration.',
                'project_url' => 'https://social-dashboard.example.com',
                'github_url' => 'https://github.com/demo/social-dashboard',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'React',
            ],
            [
                'title' => 'AI Content Generator',
                'description' => 'AI-powered content generation tool using GPT models for creating blog posts, social media content, product descriptions, and marketing copy.',
                'project_url' => 'https://ai-content.example.com',
                'github_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Python',
            ],
            [
                'title' => 'Task Management SaaS',
                'description' => 'Collaborative task management platform with Kanban boards, time tracking, team collaboration, file sharing, and project analytics.',
                'project_url' => 'https://taskflow.example.com',
                'github_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Vue.js',
            ],
            [
                'title' => 'IoT Home Automation',
                'description' => 'Smart home automation system with device control, scheduling, energy monitoring, security alerts, and voice assistant integration.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/iot-home',
                'status' => 'draft',
                'is_featured' => false,
                'tech_stack' => 'Node.js',
            ],
            [
                'title' => 'FinTech Payment Gateway',
                'description' => 'Secure payment processing solution with multi-currency support, fraud detection, PCI compliance, and extensive reporting capabilities.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/payment-gateway',
                'status' => 'published',
                'is_featured' => true,
                'tech_stack' => 'Go',
            ],
            [
                'title' => 'Online Learning Platform',
                'description' => 'E-learning platform with video courses, interactive quizzes, progress tracking, certificates, and instructor tools for course management.',
                'project_url' => 'https://learnhub.example.com',
                'github_url' => 'https://github.com/demo/learnhub',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Django',
            ],
            [
                'title' => 'Restaurant POS System',
                'description' => 'Point of sale system for restaurants with order management, table tracking, kitchen display, inventory management, and sales analytics.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/restaurant-pos',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Flutter',
            ],
            [
                'title' => 'Microservices Architecture',
                'description' => 'Enterprise microservices platform with service mesh, API gateway, distributed tracing, and automated deployment pipelines.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/microservices',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Kubernetes',
            ],
            [
                'title' => 'Cryptocurrency Exchange',
                'description' => 'Cryptocurrency trading platform with real-time market data, order book, trading charts, wallet management, and secure transactions.',
                'project_url' => 'https://crypto-exchange.example.com',
                'github_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Spring Boot',
            ],
            [
                'title' => 'Fitness Tracking App',
                'description' => 'Mobile fitness application with workout tracking, nutrition logging, progress photos, challenges, and social features for motivation.',
                'project_url' => 'https://fitness-app.example.com',
                'github_url' => 'https://github.com/demo/fitness-tracker',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Flutter',
            ],
            [
                'title' => 'Cloud Storage Solution',
                'description' => 'Distributed cloud storage system with file encryption, sharing, versioning, backup, and cross-platform sync capabilities.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/cloud-storage',
                'status' => 'draft',
                'is_featured' => false,
                'tech_stack' => 'Go',
            ],
            [
                'title' => 'Chat Application',
                'description' => 'Real-time messaging application with group chats, direct messages, file sharing, voice/video calls, and end-to-end encryption.',
                'project_url' => 'https://chat-app.example.com',
                'github_url' => 'https://github.com/demo/chat-app',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'React Native',
            ],
            [
                'title' => 'Marketing Automation Tool',
                'description' => 'Marketing automation platform with email campaigns, lead scoring, customer journey mapping, and ROI analytics.',
                'project_url' => 'https://marketing-auto.example.com',
                'github_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Ruby on Rails',
            ],
            [
                'title' => 'Data Analytics Pipeline',
                'description' => 'Big data processing pipeline with ETL workflows, real-time streaming, data warehousing, and business intelligence dashboards.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/data-pipeline',
                'status' => 'published',
                'is_featured' => true,
                'tech_stack' => 'AWS',
            ],
            [
                'title' => 'Travel Booking Platform',
                'description' => 'Travel booking website with flight search, hotel reservations, car rentals, itinerary planning, and price comparison features.',
                'project_url' => 'https://travel-booking.example.com',
                'github_url' => null,
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Next.js',
            ],
            [
                'title' => 'Inventory Management',
                'description' => 'Business inventory management system with barcode scanning, multi-warehouse support, demand forecasting, and supplier management.',
                'project_url' => null,
                'github_url' => 'https://github.com/demo/inventory',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Laravel',
            ],
            [
                'title' => 'Video Streaming Service',
                'description' => 'Video-on-demand platform with adaptive streaming, content management, subscription handling, and multi-device support.',
                'project_url' => 'https://video-stream.example.com',
                'github_url' => 'https://github.com/demo/video-stream',
                'status' => 'published',
                'is_featured' => false,
                'tech_stack' => 'Node.js',
            ],
        ];

        $createdPortfolios = 0;
        $userIndex = 0;

        foreach ($portfolios as $portfolioData) {
            if ($userIndex >= $users->count()) {
                break;
            }

            $techStackName = $portfolioData['tech_stack'];
            unset($portfolioData['tech_stack']);

            $techStack = $techStacks->firstWhere('name', $techStackName);

            $portfolioData['user_id'] = $users[$userIndex]->id;
            $portfolioData['tech_stack_id'] = $techStack?->id;
            $portfolioData['project_date'] = now()->subDays(rand(30, 730))->format('Y-m-d');

            Portfolio::create($portfolioData);
            $createdPortfolios++;
            $userIndex++;
        }

        $this->command->info('✅ Portfolios created successfully!');
        $this->command->info('💼 Created '.$createdPortfolios.' portfolio projects');
    }
}
