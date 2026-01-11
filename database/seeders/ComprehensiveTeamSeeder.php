<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComprehensiveTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing teams
        Team::query()->delete();

        $users = User::where('is_active', true)->get();

        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run user seeder first.');

            return;
        }

        // Get team leads
        $david = $users->firstWhere('email', 'david.williams@test.com');
        $emily = $users->firstWhere('email', 'emily.rodriguez@test.com');
        $james = $users->firstWhere('email', 'james.wilson@test.com');
        $sarah = $users->firstWhere('email', 'sarah.johnson@test.com');

        $teams = [
            [
                'name' => 'Engineering Team Alpha',
                'description' => 'Core product development team responsible for building and maintaining our flagship platforms. Specializes in full-stack development, cloud architecture, and scalable systems.',
                'team_leader_id' => $david?->id,
                'members' => [
                    'alex.thompson@test.com',
                    'jessica.martinez@test.com',
                    'ryan.garcia@test.com',
                    'amanda.lee@test.com',
                    'michelle.davis@test.com',
                    'daniel.thompson@test.com',
                ],
            ],
            [
                'name' => 'Design Studio',
                'description' => 'Creative team responsible for UI/UX design, brand identity, and user research. Creates beautiful and intuitive experiences across all products.',
                'team_leader_id' => $emily?->id,
                'members' => [
                    'stephanie.clark@test.com',
                    'thomas.anderson@test.com',
                    'laura.white@test.com',
                ],
            ],
            [
                'name' => 'Growth Marketing',
                'description' => 'Marketing team focused on user acquisition, brand awareness, and growth strategies. Manages digital campaigns, content marketing, and market research.',
                'team_leader_id' => $james?->id,
                'members' => [
                    'lisa.robinson@test.com',
                    'mark.lewis@test.com',
                ],
            ],
            [
                'name' => 'Quality Assurance Squad',
                'description' => 'Dedicated QA team ensuring product quality through comprehensive testing, automation, and quality assurance processes.',
                'team_leader_id' => $sarah?->id,
                'members' => [
                    'kevin.harris@test.com',
                    'nancy.martin@test.com',
                ],
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'Specialized team for mobile app development across iOS and Android platforms using React Native and Flutter.',
                'team_leader_id' => $users->firstWhere('email', 'christopher.brown@test.com')?->id,
                'members' => [
                    'amanda.lee@test.com',
                    'ryan.garcia@test.com',
                ],
            ],
            [
                'name' => 'DevOps & Infrastructure',
                'description' => 'Infrastructure and operations team managing cloud resources, CI/CD pipelines, and system reliability.',
                'team_leader_id' => $users->firstWhere('email', 'michelle.davis@test.com')?->id,
                'members' => [
                    'steven.king@test.com',
                ],
            ],
            [
                'name' => 'Data & Analytics',
                'description' => 'Data science and analytics team providing insights, business intelligence, and data-driven decision support.',
                'team_leader_id' => $users->firstWhere('email', 'patricia.young@test.com')?->id,
                'members' => [
                    'jennifer.walker@test.com',
                ],
            ],
            [
                'name' => 'Product Innovation',
                'description' => 'Cross-functional team focused on innovation, R&D, and new product development initiatives.',
                'team_leader_id' => $users->firstWhere('email', 'michael.chen@test.com')?->id,
                'members' => [
                    'sarah.johnson@test.com',
                    'emily.rodriguez@test.com',
                    'david.williams@test.com',
                ],
            ],
        ];

        foreach ($teams as $teamData) {
            $memberEmails = $teamData['members'] ?? [];
            $teamLeaderId = $teamData['team_leader_id'];
            unset($teamData['members']);

            $team = Team::create($teamData);

            // Attach members to the team
            foreach ($memberEmails as $email) {
                $user = $users->firstWhere('email', $email);
                if ($user) {
                    $user->update(['team_id' => $team->id]);
                }
            }
        }

        $this->command->info('✅ Teams created successfully!');
        $this->command->info('👥 Created '.count($teams).' teams with members');
    }
}
