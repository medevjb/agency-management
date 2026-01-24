<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateRoleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a team and tech stack for team-leader and stack-leader
        $team = Team::first();
        if (! $team) {
            $team = Team::create([
                'name' => 'Development Team',
                'description' => 'Main development team',
            ]);
        }

        $techStack = TechStack::first();
        if (! $techStack) {
            $techStack = TechStack::create([
                'name' => 'Laravel',
                'description' => 'Laravel PHP Framework',
            ]);
        }

        // Define users for each role
        $roleUsers = [
            [
                'role' => 'admin',
                'name' => 'Admin User',
                'email' => 'admin@admin.com',
                'password' => 'password',
                'team_id' => null,
                'tech_stack_id' => null,
            ],
            [
                'role' => 'operation-manager',
                'name' => 'Operation Manager',
                'email' => 'operation@manager.com',
                'password' => 'password',
                'team_id' => null,
                'tech_stack_id' => null,
            ],
            [
                'role' => 'project-manager',
                'name' => 'Project Manager',
                'email' => 'project@manager.com',
                'password' => 'password',
                'team_id' => null,
                'tech_stack_id' => null,
            ],
            [
                'role' => 'team-leader',
                'name' => 'Team Leader',
                'email' => 'team@leader.com',
                'password' => 'password',
                'team_id' => $team->id,
                'tech_stack_id' => null,
            ],
            [
                'role' => 'stack-leader',
                'name' => 'Stack Leader',
                'email' => 'stack@leader.com',
                'password' => 'password',
                'team_id' => null,
                'tech_stack_id' => $techStack->id,
            ],
            [
                'role' => 'team-leader',
                'name' => 'Team Member',
                'email' => 'team@member.com',
                'password' => 'password',
                'team_id' => $team->id,
                'tech_stack_id' => null,
            ],
        ];

        // Create users and assign roles
        foreach ($roleUsers as $userData) {
            // Check if user exists
            $user = User::where('email', $userData['email'])->first();

            if (! $user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'team_id' => $userData['team_id'],
                    'tech_stack_id' => $userData['tech_stack_id'],
                ]);

                // Ensure role exists
                $role = Role::firstOrCreate(
                    ['name' => $userData['role']],
                    ['guard_name' => 'web']
                );

                // Assign role to user
                $user->assignRole($userData['role']);

                $this->command->info("✅ Created {$userData['role']}: {$userData['email']} (password: {$userData['password']})");
            } else {
                // Update existing user if needed
                $user->update([
                    'team_id' => $userData['team_id'],
                    'tech_stack_id' => $userData['tech_stack_id'],
                ]);

                // Ensure role exists and is assigned
                $role = Role::firstOrCreate(
                    ['name' => $userData['role']],
                    ['guard_name' => 'web']
                );

                if (! $user->hasRole($userData['role'])) {
                    $user->assignRole($userData['role']);
                }

                $this->command->info("🔄 Updated {$userData['role']}: {$userData['email']}");
            }
        }

        $this->command->newLine();
        $this->command->info('✅ All role users created/updated successfully!');
        $this->command->newLine();
        $this->command->comment('Default password for all users: password');
    }
}
