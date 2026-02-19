<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Profile;
use App\Models\Task;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('tech_stack_user')->truncate();
        Task::truncate();
        Order::truncate();
        Profile::truncate();
        TechStack::truncate();
        User::where('email', '!=', 'admin@pmt.com')->delete();
        Team::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Create Teams
        $teams = [
            ['name' => 'Development Team Alpha', 'description' => 'Frontend and backend development team'],
            ['name' => 'Development Team Beta', 'description' => 'Mobile development team'],
            ['name' => 'Design & QA Team', 'description' => 'UI/UX design and quality assurance'],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }

        $teamAlpha = Team::where('name', 'Development Team Alpha')->first();
        $teamBeta = Team::where('name', 'Development Team Beta')->first();
        $teamDesign = Team::where('name', 'Design & QA Team')->first();

        // 2. Create Users with roles
        // (Users creation code here...)
        $users = [
            // Team Alpha Members
            [
                'name' => 'John Smith',
                'email' => 'john.alpha@pmt.com',
                'employee_id' => 'EMP001',
                'role' => 'team-leader',
                'team' => $teamAlpha,
                'position' => 'Senior Developer',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.alpha@pmt.com',
                'employee_id' => 'EMP002',
                'role' => 'stack-leader',
                'team' => $teamAlpha,
                'position' => 'Backend Lead',
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.alpha@pmt.com',
                'employee_id' => 'EMP003',
                'role' => 'team-member',
                'team' => $teamAlpha,
                'position' => 'Full Stack Developer',
            ],
            [
                'name' => 'Emily Brown',
                'email' => 'emily.alpha@pmt.com',
                'employee_id' => 'EMP004',
                'role' => 'team-member',
                'team' => $teamAlpha,
                'position' => 'Frontend Developer',
            ],
            [
                'name' => 'David Lee',
                'email' => 'david.alpha@pmt.com',
                'employee_id' => 'EMP005',
                'role' => 'team-member',
                'team' => $teamAlpha,
                'position' => 'Junior Developer',
            ],

            // Team Beta Members
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.beta@pmt.com',
                'employee_id' => 'EMP006',
                'role' => 'team-leader',
                'team' => $teamBeta,
                'position' => 'Mobile Lead',
            ],
            [
                'name' => 'Tom Garcia',
                'email' => 'tom.beta@pmt.com',
                'employee_id' => 'EMP007',
                'role' => 'stack-leader',
                'team' => $teamBeta,
                'position' => 'iOS Developer',
            ],
            [
                'name' => 'Anna Martinez',
                'email' => 'anna.beta@pmt.com',
                'employee_id' => 'EMP008',
                'role' => 'team-member',
                'team' => $teamBeta,
                'position' => 'Android Developer',
            ],
            [
                'name' => 'Chris Taylor',
                'email' => 'chris.beta@pmt.com',
                'employee_id' => 'EMP009',
                'role' => 'team-member',
                'team' => $teamBeta,
                'position' => 'React Native Developer',
            ],

            // Design & QA Team Members
            [
                'name' => 'Jessica White',
                'email' => 'jessica.design@pmt.com',
                'employee_id' => 'EMP010',
                'role' => 'team-leader',
                'team' => $teamDesign,
                'position' => 'Design Lead',
            ],
            [
                'name' => 'Kevin Moore',
                'email' => 'kevin.design@pmt.com',
                'employee_id' => 'EMP011',
                'role' => 'team-member',
                'team' => $teamDesign,
                'position' => 'UI Designer',
            ],
            [
                'name' => 'Rachel Clark',
                'email' => 'rachel.design@pmt.com',
                'employee_id' => 'EMP012',
                'role' => 'team-member',
                'team' => $teamDesign,
                'position' => 'UX Designer',
            ],
            [
                'name' => 'Brandon Hall',
                'email' => 'brandon.qa@pmt.com',
                'employee_id' => 'EMP013',
                'role' => 'team-member',
                'team' => $teamDesign,
                'position' => 'QA Engineer',
            ],
            [
                'name' => 'Amanda Allen',
                'email' => 'amanda.qa@pmt.com',
                'employee_id' => 'EMP014',
                'role' => 'team-member',
                'team' => $teamDesign,
                'position' => 'Test Lead',
            ],

            // Operation Manager & Project Manager
            [
                'name' => 'Robert Miller',
                'email' => 'robert.om@pmt.com',
                'employee_id' => 'EMP015',
                'role' => 'operation-manager',
                'team' => null,
                'position' => 'Operations Manager',
            ],
            [
                'name' => 'Jennifer Scott',
                'email' => 'jennifer.pm@pmt.com',
                'employee_id' => 'EMP016',
                'role' => 'project-manager',
                'team' => null,
                'position' => 'Project Manager',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'employee_id' => $userData['employee_id'],
                    'password' => bcrypt('password'),
                    'position' => $userData['position'],
                    'team_id' => $userData['team']?->id,
                    'is_active' => true,
                    'phone' => '555-0100',
                    'address' => '123 Main St, City, State 12345',
                    'hire_date' => Carbon::now()->subMonths(rand(1, 24)),
                ]
            );
            $user->assignRole($userData['role']);
        }

        // 3. Create Tech Stacks for each team
        $techStacks = [
            [
                'name' => 'LAMP Stack',
                'description' => 'Linux, Apache, MySQL, PHP',
                'team' => $teamAlpha,
                'leader' => User::where('email', 'sarah.alpha@pmt.com')->first(),
                'members' => ['mike.alpha@pmt.com', 'emily.alpha@pmt.com', 'david.alpha@pmt.com'],
            ],
            [
                'name' => 'MERN Stack',
                'description' => 'MongoDB, Express, React, Node.js',
                'team' => $teamAlpha,
                'leader' => User::where('email', 'john.alpha@pmt.com')->first(),
                'members' => ['sarah.alpha@pmt.com', 'emily.alpha@pmt.com'],
            ],
            [
                'name' => 'Mobile Stack',
                'description' => 'React Native, Firebase, Redux',
                'team' => $teamBeta,
                'leader' => User::where('email', 'tom.beta@pmt.com')->first(),
                'members' => ['anna.beta@pmt.com', 'chris.beta@pmt.com'],
            ],
            [
                'name' => 'iOS Native Stack',
                'description' => 'Swift, SwiftUI, Core Data',
                'team' => $teamBeta,
                'leader' => User::where('email', 'lisa.beta@pmt.com')->first(),
                'members' => ['tom.beta@pmt.com'],
            ],
            [
                'name' => 'Design Tools',
                'description' => 'Figma, Adobe XD, Sketch',
                'team' => $teamDesign,
                'leader' => User::where('email', 'jessica.design@pmt.com')->first(),
                'members' => ['kevin.design@pmt.com', 'rachel.design@pmt.com'],
            ],
        ];

        foreach ($techStacks as $stackData) {
            $techStack = TechStack::create([
                'name' => $stackData['name'],
                'description' => $stackData['description'],
                'team_id' => $stackData['team']->id,
                'stack_leader_id' => $stackData['leader']->id,
            ]);

            // Attach members
            foreach ($stackData['members'] as $memberEmail) {
                $member = User::where('email', $memberEmail)->first();
                if ($member) {
                    $techStack->users()->attach($member->id);
                }
            }

            // Also attach leader
            $techStack->users()->attach($stackData['leader']->id);
        }

        // 4. Create Orders (25-30 orders)
        $clients = ['Acme Corp', 'TechStart Inc', 'Global Solutions', 'Digital Dynamics', 'Cloud Systems', 'Smart Tech', 'Innovate Labs', 'Future Forward', 'Peak Performance', 'Streamline Solutions'];
        $statuses = ['nra', 'pending', 'in-progress', 'delivered', 'on-hold'];
        $priorities = ['low', 'medium', 'high'];

        for ($i = 1; $i <= 28; $i++) {
            $team = $i % 2 == 0 ? $teamAlpha : $teamBeta;

            $techStack = $team->techStacks()->inRandomOrder()->first();
            $creator = User::where('team_id', $team->id)->inRandomOrder()->first();

            $order = Order::create([
                'name' => 'Project '.$clients[array_rand($clients)].' '.$i,
                'order_id' => 'ORD-2024-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'team_id' => $team->id,
                'tech_stack_id' => $techStack?->id,
                'description' => 'Description for order '.$i,
                'actual_revenue' => rand(5000, 50000),
                'start_date' => Carbon::now()->subDays(rand(1, 60)),
                'end_date' => Carbon::now()->addDays(rand(30, 180)),
                'created_by' => $creator?->id,
            ]);

            // Assign random team members to order
            $teamMembers = User::where('team_id', $team->id)->pluck('id')->toArray();
            if (! empty($teamMembers)) {
                $assignCount = rand(1, min(3, count($teamMembers)));
                $assignedMembers = array_rand($teamMembers, $assignCount);
                if (! is_array($assignedMembers)) {
                    $assignedMembers = [$assignedMembers];
                }
                foreach ($assignedMembers as $memberKey) {
                    $order->users()->attach($teamMembers[$memberKey]);
                }
            }
        }

        // 5. Create Tasks (10-12 tasks)
        $taskNames = [
            'Implement user authentication',
            'Design database schema',
            'Create API endpoints',
            'Build responsive UI',
            'Write unit tests',
            'Setup CI/CD pipeline',
            'Code review and optimization',
            'Documentation',
            'Deploy to staging',
            'Bug fixes and improvements',
            'Performance tuning',
            'Security audit',
            'User testing',
        ];

        $taskStatuses = ['pending', 'in-progress', 'completed', 'on-hold'];
        $priorities = ['low', 'medium', 'high'];

        // Get all team leaders and users first
        $teamLeaders = User::role('team-leader')->get();
        $allUsers = User::all();

        for ($i = 1; $i <= 12; $i++) {
            $creator = $teamLeaders->random();
            $assignee = $allUsers->random();

            $task = Task::create([
                'title' => $taskNames[$i - 1],
                'description' => 'Detailed description for '.$taskNames[$i - 1],
                'status' => $taskStatuses[array_rand($taskStatuses)],
                'priority' => $priorities[array_rand($priorities)],
                'due_date' => Carbon::now()->addDays(rand(7, 30)),
                'team_id' => $creator->team_id,
                'created_by' => $creator->id,
                'assigned_to' => $assignee->id,
                'estimated_hours' => rand(2, 40),
                'actual_hours' => rand(0, 35),
            ]);
        }

        // 6. Create Profiles (5-6 profiles)
        $profiles = [
            [
                'profile_name' => 'John Freelancer',
                'username' => 'freelancer_john',
                'skills' => ['PHP', 'Laravel', 'React', 'MySQL'],
                'hourly_rate' => 75,
            ],
            [
                'profile_name' => 'Sarah Developer',
                'username' => 'sarah_dev',
                'skills' => ['Python', 'Django', 'PostgreSQL', 'Docker'],
                'hourly_rate' => 85,
            ],
            [
                'profile_name' => 'Mike Full Stack',
                'username' => 'mike_fullstack',
                'skills' => ['JavaScript', 'Node.js', 'MongoDB', 'AWS'],
                'hourly_rate' => 90,
            ],
            [
                'profile_name' => 'Emily Frontend',
                'username' => 'emily_frontend',
                'skills' => ['React', 'Vue.js', 'TypeScript', 'Tailwind CSS'],
                'hourly_rate' => 70,
            ],
            [
                'profile_name' => 'David Mobile Dev',
                'username' => 'david_mobile',
                'skills' => ['React Native', 'Flutter', 'Swift', 'Kotlin'],
                'hourly_rate' => 95,
            ],
            [
                'profile_name' => 'Lisa Designer',
                'username' => 'lisa_uiux',
                'skills' => ['Figma', 'Adobe XD', 'Sketch', 'Photoshop'],
                'hourly_rate' => 80,
            ],
        ];

        foreach ($profiles as $profileData) {
            Profile::create([
                'profile_name' => $profileData['profile_name'],
                'username' => $profileData['username'],
                'skills' => json_encode($profileData['skills']),
                'hourly_rate' => $profileData['hourly_rate'],
                'bio' => 'Experienced professional with expertise in '.implode(', ', $profileData['skills']),
                'status' => rand(0, 1) === 1 ? 'active' : 'inactive',
                'is_verified' => rand(0, 1) === 1,
                'is_featured' => rand(0, 1) === 1,
                'rating' => rand(30, 50) / 10, // 3.0 to 5.0
                'total_jobs' => rand(5, 100),
            ]);
        }

        $this->command->info('Comprehensive data seeding completed successfully!');
        $this->command->info('Created:');
        $this->command->info('  - '.Team::count().' teams');
        $this->command->info('  - '.User::count().' users');
        $this->command->info('  - '.TechStack::count().' tech stacks');
        $this->command->info('  - '.Order::count().' orders');
        $this->command->info('  - '.Task::count().' tasks');
        $this->command->info('  - '.Profile::count().' profiles');
    }
}
