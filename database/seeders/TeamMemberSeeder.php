<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeamMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Development Team
        $team = Team::where('name', 'Development Team')->first();

        if (! $team) {
            $this->command->error('Development Team not found! Please run CreateRoleUsersSeeder first.');

            return;
        }

        // Define team members
        $teamMembers = [
            [
                'name' => 'John Developer',
                'email' => 'john@dev.com',
                'position' => 'Senior Developer',
            ],
            [
                'name' => 'Jane Coder',
                'email' => 'jane@dev.com',
                'position' => 'Full Stack Developer',
            ],
            [
                'name' => 'Mike Programmer',
                'email' => 'mike@dev.com',
                'position' => 'Backend Developer',
            ],
            [
                'name' => 'Sarah Designer',
                'email' => 'sarah@dev.com',
                'position' => 'UI/UX Designer',
            ],
            [
                'name' => 'Tom Tester',
                'email' => 'tom@dev.com',
                'position' => 'QA Engineer',
            ],
            [
                'name' => 'Lisa Analyst',
                'email' => 'lisa@dev.com',
                'position' => 'Business Analyst',
            ],
            [
                'name' => 'Chris DevOps',
                'email' => 'chris@dev.com',
                'position' => 'DevOps Engineer',
            ],
            [
                'name' => 'Amy Frontend',
                'email' => 'amy@dev.com',
                'position' => 'Frontend Developer',
            ],
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($teamMembers as $member) {
            // Check if user exists
            $user = User::where('email', $member['email'])->first();

            if (! $user) {
                // Create new user
                $user = User::create([
                    'name' => $member['name'],
                    'email' => $member['email'],
                    'password' => Hash::make('password'),
                    'team_id' => $team->id,
                ]);

                $createdCount++;
                $this->command->info("✅ Created {$member['name']} ({$member['position']})");
            } else {
                // Update existing user's team
                $user->update([
                    'team_id' => $team->id,
                ]);

                $updatedCount++;
                $this->command->info("🔄 Updated {$member['name']} - added to team");
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Successfully created {$createdCount} new team members");
        $this->command->info("🔄 Updated {$updatedCount} existing users");
        $this->command->newLine();
        $this->command->comment('Team: Development Team');
        $this->command->comment('Total team members: '.($createdCount + $updatedCount + 2)); // +2 for team leader and existing team member
        $this->command->newLine();
        $this->command->comment('Default password for all users: password');
    }
}
