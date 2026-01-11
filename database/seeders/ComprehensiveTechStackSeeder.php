<?php

namespace Database\Seeders;

use App\Models\TechStack;
use Illuminate\Database\Seeder;

class ComprehensiveTechStackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing tech stacks
        TechStack::query()->delete();

        $techStacks = [
            [
                'name' => 'Laravel',
                'description' => 'Elegant PHP framework for web artisans with expressive syntax and powerful features for modern web application development.',
            ],
            [
                'name' => 'React',
                'description' => 'Popular JavaScript library for building user interfaces with component-based architecture and virtual DOM for optimal performance.',
            ],
            [
                'name' => 'Vue.js',
                'description' => 'Progressive JavaScript framework for building modern web interfaces with reactive components and excellent documentation.',
            ],
            [
                'name' => 'Node.js',
                'description' => 'JavaScript runtime built on Chrome\'s V8 engine for building scalable network applications and server-side programming.',
            ],
            [
                'name' => 'Python',
                'description' => 'Versatile programming language for web development, data science, AI/ML, automation, and backend services.',
            ],
            [
                'name' => 'Django',
                'description' => 'High-level Python web framework with batteries-included approach for rapid development and clean design.',
            ],
            [
                'name' => 'Ruby on Rails',
                'description' => 'Full-stack web framework optimized for programmer happiness and sustainable productivity with convention over configuration.',
            ],
            [
                'name' => 'Flutter',
                'description' => 'UI toolkit from Google for building natively compiled applications for mobile, web, and desktop from a single codebase.',
            ],
            [
                'name' => 'React Native',
                'description' => 'Framework for building native mobile apps using React and JavaScript, enabling cross-platform mobile development.',
            ],
            [
                'name' => 'Angular',
                'description' => 'Platform for building mobile and desktop web applications using TypeScript with powerful features and large community.',
            ],
            [
                'name' => 'Next.js',
                'description' => 'React framework with server-side rendering, static site generation, and optimized performance for modern web apps.',
            ],
            [
                'name' => 'Nuxt.js',
                'description' => 'Vue.js framework for universal applications with server-side rendering, static generation, and automatic routing.',
            ],
            [
                'name' => 'Express.js',
                'description' => 'Fast, unopinionated, minimalist web framework for Node.js to build web applications and APIs.',
            ],
            [
                'name' => 'Spring Boot',
                'description' => 'Java-based framework for creating production-ready Spring applications with minimal configuration and maximum productivity.',
            ],
            [
                'name' => 'ASP.NET Core',
                'description' => 'Cross-platform framework for building modern, cloud-based, internet-connected applications from Microsoft.',
            ],
            [
                'name' => 'Go',
                'description' => 'Statically typed language from Google for building simple, reliable, and efficient software at scale.',
            ],
            [
                'name' => 'Rust',
                'description' => 'Systems programming language focused on safety, speed, and concurrency for building high-performance applications.',
            ],
            [
                'name' => 'Swift',
                'description' => 'Powerful and intuitive programming language from Apple for iOS, macOS, watchOS, and tvOS app development.',
            ],
            [
                'name' => 'Kotlin',
                'description' => 'Modern, concise, and safe programming language for Android development, server-side, and multiplatform projects.',
            ],
            [
                'name' => 'Docker',
                'description' => 'Platform for developing, shipping, and running applications in containers for consistent deployment across environments.',
            ],
            [
                'name' => 'Kubernetes',
                'description' => 'Container orchestration platform for automating deployment, scaling, and management of containerized applications.',
            ],
            [
                'name' => 'AWS',
                'description' => 'Amazon Web Services cloud platform offering computing power, database storage, content delivery, and more.',
            ],
            [
                'name' => 'Azure',
                'description' => 'Microsoft cloud platform with services for computing, analytics, storage, and networking for enterprise solutions.',
            ],
            [
                'name' => 'Google Cloud',
                'description' => 'Google Cloud Platform for data analytics, machine learning, and cloud infrastructure with excellent developer tools.',
            ],
            [
                'name' => 'MongoDB',
                'description' => 'NoSQL document database for flexible, scalable data storage with rich querying and indexing capabilities.',
            ],
            [
                'name' => 'PostgreSQL',
                'description' => 'Powerful, open-source object-relational database system with reliability, data integrity, and robust features.',
            ],
            [
                'name' => 'MySQL',
                'description' => 'Popular open-source relational database management system for web applications and enterprise data storage.',
            ],
            [
                'name' => 'Redis',
                'description' => 'In-memory data structure store used as database, cache, message broker, and session store for high performance.',
            ],
            [
                'name' => 'Elasticsearch',
                'description' => 'Distributed search and analytics engine for log analytics, full-text search, security intelligence, and operational monitoring.',
            ],
            [
                'name' => 'TensorFlow',
                'description' => 'Open-source machine learning platform for building and deploying ML models with comprehensive tools and libraries.',
            ],
        ];

        foreach ($techStacks as $techStack) {
            TechStack::create($techStack);
        }

        $this->command->info('✅ Tech stacks created successfully!');
        $this->command->info('💻 Created '.count($techStacks).' technology stacks');
    }
}
