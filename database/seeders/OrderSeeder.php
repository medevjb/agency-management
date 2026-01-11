<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Profile;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create teams and tech stacks
        $devTeam = Team::firstOrCreate(['name' => 'Development Team'], [
            'description' => 'Core software development team responsible for building and maintaining applications.',
        ]);

        $marketingTeam = Team::firstOrCreate(['name' => 'Marketing Team'], [
            'description' => 'Marketing and growth team responsible for product promotion and user acquisition.',
        ]);

        $designTeam = Team::firstOrCreate(['name' => 'Design Team'], [
            'description' => 'UI/UX design team responsible for user experience and visual design.',
        ]);

        $salesTeam = Team::firstOrCreate(['name' => 'Sales Team'], [
            'description' => 'Sales team responsible for client relationships and business development.',
        ]);

        $laravelStack = TechStack::firstOrCreate(['name' => 'Laravel'], [
            'description' => 'PHP framework for web application development with elegant syntax and powerful features.',
        ]);

        $reactStack = TechStack::firstOrCreate(['name' => 'React'], [
            'description' => 'JavaScript library for building user interfaces with component-based architecture.',
        ]);

        $vueStack = TechStack::firstOrCreate(['name' => 'Vue.js'], [
            'description' => 'Progressive JavaScript framework for building interactive web interfaces.',
        ]);

        $pythonStack = TechStack::firstOrCreate(['name' => 'Python'], [
            'description' => 'High-level programming language for data analysis, AI, and backend development.',
        ]);

        $nodeStack = TechStack::firstOrCreate(['name' => 'Node.js'], [
            'description' => 'JavaScript runtime built for scalable network applications and server-side programming.',
        ]);

        // Get existing users
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UsersWithRolesSeeder first.');

            return;
        }

        $admin = $users->firstWhere('email', 'admin@test.com') ?? $users->first();

        // Get or create profiles for freelance assignments
        $profiles = Profile::all();
        if ($profiles->isEmpty()) {
            // Create some demo profiles if none exist
            $profiles = collect([
                Profile::create([
                    'user_id' => $admin->id,
                    'profile_name' => 'John Dev',
                    'username' => 'johndev',
                    'bio' => 'Full-stack developer specializing in Laravel and React',
                    'skills' => 'Laravel,React,PHP,JavaScript',
                    'hourly_rate' => '75',
                    'status' => 'active',
                    'is_verified' => true,
                    'rating' => 5,
                    'total_jobs' => 45,
                ]),
                Profile::create([
                    'user_id' => $admin->id,
                    'profile_name' => 'Sarah Designer',
                    'username' => 'sarahdesign',
                    'bio' => 'UI/UX designer with 8 years of experience',
                    'skills' => 'Figma,Sketch,Adobe XD,UI Design',
                    'hourly_rate' => '65',
                    'status' => 'active',
                    'is_verified' => true,
                    'rating' => 5,
                    'total_jobs' => 32,
                ]),
                Profile::create([
                    'user_id' => $admin->id,
                    'profile_name' => 'Mike Python',
                    'username' => 'mikepython',
                    'bio' => 'Data scientist and Python developer',
                    'skills' => 'Python,Pandas,NumPy,Machine Learning',
                    'hourly_rate' => '85',
                    'status' => 'active',
                    'is_verified' => true,
                    'rating' => 4,
                    'total_jobs' => 28,
                ]),
            ]);
        }

        // Clear existing orders to avoid duplicates (including soft deletes)
        Order::query()->forceDelete();

        // 20 Realistic Orders with current/future dates and amounts
        $orders = [
            [
                'order_id' => 'ORD-001',
                'name' => 'E-Commerce Platform Redesign',
                'description' => 'Complete redesign of existing e-commerce platform with modern UI/UX, improved performance, mobile optimization, and advanced features including AI-powered product recommendations, real-time inventory management, multi-currency support, and enhanced security measures.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2025-11-15',
                'end_date' => '2026-04-30',
                'value' => 125000.00,
                'actual_revenue' => 75000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-002',
                'name' => 'Mobile Banking Application',
                'description' => 'Develop a secure mobile banking application with biometric authentication, real-time transaction alerts, bill payment features, peer-to-peer transfers, card management, investment tracking, and comprehensive security including 2FA and fraud detection.',
                'status' => 'planning',
                'priority' => 'urgent',
                'start_date' => '2026-01-01',
                'end_date' => '2026-08-31',
                'value' => 250000.00,
                'actual_revenue' => 50000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-003',
                'name' => 'Corporate Website Development',
                'description' => 'Build a professional corporate website with content management system, multi-language support, SEO optimization, blog functionality, contact forms, newsletter integration, and analytics dashboard for marketing team.',
                'status' => 'completed',
                'priority' => 'medium',
                'start_date' => '2025-09-01',
                'end_date' => '2025-12-15',
                'value' => 45000.00,
                'actual_revenue' => 48000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $vueStack->id,
                'freelance_profile_id' => $profiles->get(1)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-004',
                'name' => 'Healthcare Management System',
                'description' => 'Comprehensive healthcare platform including patient records management, appointment scheduling, telemedicine video consultations, prescription management, medical billing, insurance integration, and HIPAA-compliant data storage.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2025-10-01',
                'end_date' => '2026-05-31',
                'value' => 180000.00,
                'actual_revenue' => 95000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-005',
                'name' => 'Brand Identity & Marketing Materials',
                'description' => 'Complete brand identity package including logo design, brand guidelines, marketing collateral, business cards, letterheads, brochures, social media templates, presentation templates, and comprehensive brand strategy document.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'start_date' => '2025-11-01',
                'end_date' => '2026-03-31',
                'value' => 35000.00,
                'actual_revenue' => 22000.00,
                'team_id' => $designTeam->id,
                'tech_stack_id' => null,
                'freelance_profile_id' => $profiles->get(1)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-006',
                'name' => 'Learning Management System',
                'description' => 'Enterprise LMS with video hosting, interactive course builder, progress tracking, live virtual classrooms, assignment submissions, quiz engine, certification generation, gamification features, and detailed reporting for administrators.',
                'status' => 'completed',
                'priority' => 'high',
                'start_date' => '2025-08-01',
                'end_date' => '2025-12-10',
                'value' => 95000.00,
                'actual_revenue' => 102000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $vueStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-007',
                'name' => 'Real Estate Platform',
                'description' => 'Full-featured real estate listing platform with advanced search filters, virtual tours, map integration, mortgage calculator, agent profiles, lead management, CRM integration, and mobile-responsive design.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2025-10-15',
                'end_date' => '2026-06-30',
                'value' => 110000.00,
                'actual_revenue' => 68000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-008',
                'name' => 'Data Analytics Dashboard',
                'description' => 'Business intelligence dashboard with real-time data visualization, custom report builder, predictive analytics, automated insights, data export capabilities, multi-source integration, and role-based access control.',
                'status' => 'planning',
                'priority' => 'medium',
                'start_date' => '2026-02-01',
                'end_date' => '2026-07-31',
                'value' => 88000.00,
                'actual_revenue' => 25000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $pythonStack->id,
                'freelance_profile_id' => $profiles->get(2)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-009',
                'name' => 'Social Media Marketing Campaign',
                'description' => 'Comprehensive social media marketing strategy including content calendar creation, post design and copywriting, community management, paid advertising campaigns, influencer partnerships, and detailed analytics reporting.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'start_date' => '2025-11-01',
                'end_date' => '2026-02-28',
                'value' => 42000.00,
                'actual_revenue' => 28500.00,
                'team_id' => $marketingTeam->id,
                'tech_stack_id' => null,
                'freelance_profile_id' => $profiles->get(1)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-010',
                'name' => 'Restaurant POS & Delivery System',
                'description' => 'Complete restaurant management solution with POS system, table management, kitchen display system, inventory tracking, delivery management, customer loyalty program, online ordering, and detailed sales analytics.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2025-09-15',
                'end_date' => '2026-04-15',
                'value' => 75000.00,
                'actual_revenue' => 52000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $nodeStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-011',
                'name' => 'Fitness Tracking Mobile App',
                'description' => 'Comprehensive fitness app with workout tracking, nutrition logging, progress photos, workout plans, challenges and competitions, social features, wearable device integration, and AI-powered personalized recommendations.',
                'status' => 'planning',
                'priority' => 'medium',
                'start_date' => '2026-01-15',
                'end_date' => '2026-09-30',
                'value' => 145000.00,
                'actual_revenue' => 35000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-012',
                'name' => 'Inventory Management System',
                'description' => 'Enterprise-grade inventory management with barcode scanning, multi-warehouse support, supplier management, automated reordering, demand forecasting, quality control, audit trails, and comprehensive reporting.',
                'status' => 'completed',
                'priority' => 'high',
                'start_date' => '2025-07-01',
                'end_date' => '2025-11-30',
                'value' => 92000.00,
                'actual_revenue' => 98000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-013',
                'name' => 'SEO Optimization Package',
                'description' => 'Complete SEO audit and optimization including technical SEO fixes, keyword research, on-page optimization, content strategy, link building campaign, local SEO, performance optimization, and monthly progress reports.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'start_date' => '2025-11-15',
                'end_date' => '2026-05-15',
                'value' => 28000.00,
                'actual_revenue' => 16500.00,
                'team_id' => $marketingTeam->id,
                'tech_stack_id' => null,
                'freelance_profile_id' => $profiles->get(1)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-014',
                'name' => 'Customer Support Portal',
                'description' => 'Self-service customer support portal with knowledge base, ticket submission, live chat, chatbot integration, FAQ management, video tutorials, community forum, and multi-channel support integration.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2025-10-01',
                'end_date' => '2026-06-30',
                'value' => 68000.00,
                'actual_revenue' => 41000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $vueStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-015',
                'name' => 'Video Streaming Platform',
                'description' => 'Video-on-demand platform with live streaming capabilities, adaptive bitrate streaming, content management, subscription management, payment integration, analytics dashboard, DRM protection, and mobile apps.',
                'status' => 'planning',
                'priority' => 'urgent',
                'start_date' => '2026-03-01',
                'end_date' => '2026-10-31',
                'value' => 195000.00,
                'actual_revenue' => 45000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $nodeStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-016',
                'name' => 'CRM Implementation',
                'description' => 'Custom CRM system development with lead management, contact database, sales pipeline tracking, email automation, task management, document storage, reporting dashboards, and third-party integrations.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2025-09-20',
                'end_date' => '2026-05-30',
                'value' => 115000.00,
                'actual_revenue' => 72000.00,
                'team_id' => $salesTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-017',
                'name' => 'E-Learning Mobile App',
                'description' => 'Mobile learning application with offline course access, interactive quizzes, video playback, progress synchronization, discussion forums, push notifications, certificate download, and social learning features.',
                'status' => 'completed',
                'priority' => 'medium',
                'start_date' => '2025-08-15',
                'end_date' => '2025-12-20',
                'value' => 78000.00,
                'actual_revenue' => 82000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-018',
                'name' => 'Event Management Platform',
                'description' => 'Comprehensive event management with event creation, ticket sales, attendee registration, seating management, agenda builder, networking features, mobile event app, virtual event support, and detailed analytics.',
                'status' => 'in-progress',
                'priority' => 'high',
                'start_date' => '2025-10-15',
                'end_date' => '2026-07-31',
                'value' => 135000.00,
                'actual_revenue' => 78000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-019',
                'name' => 'Payment Gateway Integration',
                'description' => 'Integration of multiple payment gateways including Stripe, PayPal, Square, and local payment methods with PCI compliance, fraud detection, subscription billing, payment plans, refund processing, and comprehensive transaction logging.',
                'status' => 'planning',
                'priority' => 'urgent',
                'start_date' => '2026-02-01',
                'end_date' => '2026-05-31',
                'value' => 58000.00,
                'actual_revenue' => 15000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $nodeStack->id,
                'freelance_profile_id' => $profiles->get(0)?->id,
                'created_by' => $admin->id,
            ],
            [
                'order_id' => 'ORD-020',
                'name' => 'AI Chatbot Development',
                'description' => 'Custom AI-powered chatbot with natural language processing, intent recognition, context awareness, multi-language support, integration with business systems, learning capabilities, analytics dashboard, and human handoff functionality.',
                'status' => 'in-progress',
                'priority' => 'medium',
                'start_date' => '2025-11-01',
                'end_date' => '2026-08-31',
                'value' => 89000.00,
                'actual_revenue' => 49000.00,
                'team_id' => $devTeam->id,
                'tech_stack_id' => $pythonStack->id,
                'freelance_profile_id' => $profiles->get(2)?->id,
                'created_by' => $admin->id,
            ],
        ];

        // Create all orders
        foreach ($orders as $orderData) {
            Order::create($orderData);
        }

        $this->command->info('✅ Orders created successfully!');
        $this->command->info('📊 Created '.count($orders).' comprehensive orders');
    }
}
