<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComprehensiveProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing profiles
        Profile::query()->forceDelete();

        $users = User::where('is_active', true)->get();

        if ($users->isEmpty()) {
            $this->command->error('No active users found. Please run user seeder first.');

            return;
        }

        $profiles = [
            [
                'profile_name' => 'CodeMaster Pro',
                'username' => 'codemaster',
                'bio' => 'Full-stack developer with 10+ years of experience building scalable web applications. Expert in Laravel, React, and cloud architecture.',
                'skills' => 'Laravel,React,Vue,Node.js,Python,AWS,Docker,PostgreSQL,MongoDB,Redis',
                'hourly_rate' => '95',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => true,
                'rating' => 5,
                'total_jobs' => 87,
            ],
            [
                'profile_name' => 'Design Ninja',
                'username' => 'designninja',
                'bio' => 'Award-winning UI/UX designer specializing in creating beautiful, intuitive user experiences. 8 years of design expertise.',
                'skills' => 'Figma,Sketch,Adobe XD,Photoshop,Illustrator,Prototyping,User Research,Design Systems',
                'hourly_rate' => '85',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => true,
                'rating' => 5,
                'total_jobs' => 64,
            ],
            [
                'profile_name' => 'Data Wizard',
                'username' => 'datawizard',
                'bio' => 'Data scientist and ML engineer passionate about turning data into insights. PhD in Computer Science with expertise in deep learning.',
                'skills' => 'Python,Pandas,NumPy,TensorFlow,PyTorch,Scikit-learn,SQL,Tableau,Power BI',
                'hourly_rate' => '110',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => true,
                'rating' => 5,
                'total_jobs' => 42,
            ],
            [
                'profile_name' => 'Mobile Dev Star',
                'username' => 'mobiledevstar',
                'bio' => 'Mobile app developer specializing in React Native and Flutter. Built 50+ apps with millions of downloads combined.',
                'skills' => 'React Native,Flutter,Swift,Kotlin,iOS,Android,Firebase,Mobile UI/UX',
                'hourly_rate' => '90',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 5,
                'total_jobs' => 56,
            ],
            [
                'profile_name' => 'Backend Boss',
                'username' => 'backendboss',
                'bio' => 'Backend engineer specializing in high-performance APIs and microservices. Experience with systems handling millions of requests per day.',
                'skills' => 'Laravel,Node.js,Go,Java,MySQL,PostgreSQL,Redis,Elasticsearch,Kafka',
                'hourly_rate' => '100',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 5,
                'total_jobs' => 73,
            ],
            [
                'profile_name' => 'DevOps Guru',
                'username' => 'devopsguru',
                'bio' => 'DevOps engineer with expertise in cloud infrastructure, CI/CD pipelines, and automation. AWS and Azure certified.',
                'skills' => 'AWS,Azure,Docker,Kubernetes,Terraform,Ansible,Jenkins,GitLab CI,Linux,Bash',
                'hourly_rate' => '105',
                'status' => 'busy',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 5,
                'total_jobs' => 38,
            ],
            [
                'profile_name' => 'Frontend Fanatic',
                'username' => 'frontendfanatic',
                'bio' => 'Frontend developer passionate about creating pixel-perfect, performant user interfaces with modern frameworks.',
                'skills' => 'React,Vue,Angular,TypeScript,Next.js, Tailwind CSS,JavaScript,HTML,CSS',
                'hourly_rate' => '80',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 4,
                'total_jobs' => 51,
            ],
            [
                'profile_name' => 'QA Queen',
                'username' => 'qaqueen',
                'bio' => 'Quality assurance specialist with expertise in automated testing, test frameworks, and quality assurance methodologies.',
                'skills' => 'Selenium,Cypress,JUnit,TestNG,Postman,API Testing,Manual Testing,Agile',
                'hourly_rate' => '75',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 4,
                'total_jobs' => 69,
            ],
            [
                'profile_name' => 'CMS Master',
                'username' => 'cmsmaster',
                'bio' => 'WordPress and CMS expert with 12 years of experience. Built 200+ custom themes and plugins.',
                'skills' => 'WordPress,Drupal,Shopify,WooCommerce,PHP,MySQL,JQuery,Elementor,Divi',
                'hourly_rate' => '70',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 4,
                'total_jobs' => 203,
            ],
            [
                'profile_name' => 'E-commerce Expert',
                'username' => 'ecomexpert',
                'bio' => 'E-commerce specialist with expertise in Shopify, WooCommerce, and custom online store development.',
                'skills' => 'Shopify,WooCommerce,Magento,BigCommerce,Payment Gateways,Stripe,PayPal',
                'hourly_rate' => '88',
                'status' => 'away',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 5,
                'total_jobs' => 94,
            ],
            [
                'profile_name' => 'API Architect',
                'username' => 'apiarchitect',
                'bio' => 'API design and integration specialist. Expert in RESTful APIs, GraphQL, and microservices architecture.',
                'skills' => 'REST API,GraphQL,SOAP,API Design,Postman,Swagger,OpenAPI,Integration',
                'hourly_rate' => '92',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 4,
                'total_jobs' => 47,
            ],
            [
                'profile_name' => 'Cloud Native',
                'username' => 'cloudnative',
                'bio' => 'Cloud architect specializing in serverless applications and cloud-native development patterns.',
                'skills' => 'AWS Lambda,Azure Functions,Google Cloud Functions,Serverless,Terraform,CloudFormation',
                'hourly_rate' => '108',
                'status' => 'busy',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 5,
                'total_jobs' => 31,
            ],
            [
                'profile_name' => 'Blockchain Builder',
                'username' => 'blockchainbuilder',
                'bio' => 'Blockchain developer with expertise in smart contracts and decentralized applications.',
                'skills' => 'Solidity,Ethereum,Web3.js,Smart Contracts,DeFi,NFT,Blockchain,Try to be Rust',
                'hourly_rate' => '120',
                'status' => 'active',
                'is_verified' => false,
                'is_featured' => false,
                'rating' => 4,
                'total_jobs' => 23,
            ],
            [
                'profile_name' => 'AI Engineer',
                'username' => 'aiengineer',
                'bio' => 'AI/ML engineer specializing in natural language processing, computer vision, and deep learning applications.',
                'skills' => 'Python,TensorFlow,PyTorch,NLP,Computer Vision,Deep Learning,OpenAI API,GPT',
                'hourly_rate' => '115',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 5,
                'total_jobs' => 28,
            ],
            [
                'profile_name' => 'Security Specialist',
                'username' => 'secspecialist',
                'bio' => 'Cybersecurity expert with certifications in CISSP and CEH. Specialize in application security and penetration testing.',
                'skills' => 'Penetration Testing,Security Auditing,OWASP,Network Security,Encryption,Authentication',
                'hourly_rate' => '112',
                'status' => 'active',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 5,
                'total_jobs' => 35,
            ],
            [
                'profile_name' => 'Content Creator',
                'username' => 'contentcreator',
                'bio' => 'Technical writer and content creator specializing in developer documentation, tutorials, and blog posts.',
                'skills' => 'Technical Writing,Documentation,Content Strategy,SEO,Copywriting,Markdown',
                'hourly_rate' => '65',
                'status' => 'inactive',
                'is_verified' => true,
                'is_featured' => false,
                'rating' => 4,
                'total_jobs' => 112,
            ],
        ];

        $createdProfiles = 0;
        $userIndex = 0;

        foreach ($profiles as $profileData) {
            if ($userIndex >= $users->count()) {
                break;
            }

            $profileData['user_id'] = $users[$userIndex]->id;
            Profile::create($profileData);
            $createdProfiles++;
            $userIndex++;
        }

        $this->command->info('✅ Profiles created successfully!');
        $this->command->info('👤 Created '.$createdProfiles.' freelancer profiles');
    }
}
