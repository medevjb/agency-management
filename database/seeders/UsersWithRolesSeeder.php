<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create teams and tech stacks first
        $devTeam = Team::firstOrCreate([
            'name' => 'Development Team',
            'description' => 'Core development team',
        ]);

        $designTeam = Team::firstOrCreate([
            'name' => 'Design Team',
            'description' => 'UI/UX design team',
        ]);

        $laravelStack = TechStack::firstOrCreate([
            'name' => 'Laravel',
            'description' => 'PHP Framework',
        ]);

        $reactStack = TechStack::firstOrCreate([
            'name' => 'React',
            'description' => 'JavaScript Library',
        ]);

        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        $superAdmin->assignRole('admin');

        // Create Admin (general admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
            ]
        );
        $admin->assignRole('admin');

        // Create Operation Manager
        $opManager = User::firstOrCreate(
            ['email' => 'opmanager@test.com'],
            [
                'name' => 'John Operation',
                'password' => Hash::make('password'),
                'role' => 'operation-manager',
                'team_id' => $devTeam->id,
            ]
        );
        $opManager->assignRole('operation-manager');

        // Create Team Leaders
        $teamLeader1 = User::firstOrCreate(
            ['email' => 'teamlead1@test.com'],
            [
                'name' => 'Sarah Team Lead',
                'password' => Hash::make('password'),
                'role' => 'team-leader',
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
            ]
        );
        $teamLeader1->assignRole('team-leader');
        $devTeam->update(['team_leader_id' => $teamLeader1->id]);

        $teamLeader2 = User::firstOrCreate(
            ['email' => 'teamlead2@test.com'],
            [
                'name' => 'Mike Design Lead',
                'password' => Hash::make('password'),
                'role' => 'team-leader',
                'team_id' => $designTeam->id,
            ]
        );
        $teamLeader2->assignRole('team-leader');
        $designTeam->update(['team_leader_id' => $teamLeader2->id]);

        // Create Stack Leaders
        $stackLeader1 = User::firstOrCreate(
            ['email' => 'stacklead1@test.com'],
            [
                'name' => 'Emma Laravel Expert',
                'password' => Hash::make('password'),
                'role' => 'stack-leader',
                'tech_stack_id' => $laravelStack->id,
            ]
        );
        $stackLeader1->assignRole('stack-leader');
        $laravelStack->update(['stack_leader_id' => $stackLeader1->id]);

        $stackLeader2 = User::firstOrCreate(
            ['email' => 'stacklead2@test.com'],
            [
                'name' => 'Alex React Expert',
                'password' => Hash::make('password'),
                'role' => 'stack-leader',
                'tech_stack_id' => $reactStack->id,
            ]
        );
        $stackLeader2->assignRole('stack-leader');
        $reactStack->update(['stack_leader_id' => $stackLeader2->id]);

        // Create Team Members
        $teamMembers = [
            [
                'name' => 'David Developer',
                'email' => 'dev1@test.com',
                'role' => 'team-member',
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
            ],
            [
                'name' => 'Lisa Developer',
                'email' => 'dev2@test.com',
                'role' => 'team-member',
                'team_id' => $devTeam->id,
                'tech_stack_id' => $laravelStack->id,
            ],
            [
                'name' => 'Chris Developer',
                'email' => 'dev3@test.com',
                'role' => 'team-member',
                'team_id' => $devTeam->id,
                'tech_stack_id' => $reactStack->id,
            ],
            [
                'name' => 'Sophie Designer',
                'email' => 'designer1@test.com',
                'role' => 'team-member',
                'team_id' => $designTeam->id,
            ],
            [
                'name' => 'Robert Designer',
                'email' => 'designer2@test.com',
                'role' => 'team-member',
                'team_id' => $designTeam->id,
            ],
        ];

        foreach ($teamMembers as $member) {
            $user = User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make('password'),
                    'role' => $member['role'],
                    'team_id' => $member['team_id'] ?? null,
                    'tech_stack_id' => $member['tech_stack_id'] ?? null,
                ]
            );
            $user->assignRole('team-member');
        }

        // Designation users: GM, OP, PM, T.L, Stack Leader, Team Member (6 roles)
        $designationUsers = [
            ['email' => 'tonmoy@gmail.com', 'name' => 'Tonmoy', 'role' => 'admin', 'designation' => 'GM'],
            ['email' => 'jb@gmail.com', 'name' => 'JB', 'role' => 'operation-manager', 'designation' => 'OP'],
            ['email' => 'Saifur@gmail.com', 'name' => 'Saifur', 'role' => 'project-manager', 'designation' => 'PM'],
            ['email' => 'tarek@gmail.com', 'name' => 'Tarek', 'role' => 'team-leader', 'designation' => 'T.L'],
            ['email' => 'helal@gmail.com', 'name' => 'Helal', 'role' => 'stack-leader', 'designation' => 'Stack Leader'],
            ['email' => 'emon@gmail.com', 'name' => 'Emon', 'role' => 'team-member', 'designation' => 'Team Member'],
        ];

        foreach ($designationUsers as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'password' => Hash::make('password'),
                    'role' => $d['role'],
                    'team_id' => null,
                    'tech_stack_id' => null,
                ]
            );
            $user->syncRoles([$d['role']]);
            $user->update(['role' => $d['role']]);

            if ($d['role'] === 'team-leader') {
                $user->update(['team_id' => $devTeam->id]);
                $devTeam->update(['team_leader_id' => $user->id]);
            }
            if ($d['role'] === 'stack-leader') {
                $user->update(['tech_stack_id' => $laravelStack->id]);
                $laravelStack->update(['stack_leader_id' => $user->id]);
            }
            if ($d['role'] === 'team-member') {
                $user->update(['team_id' => $devTeam->id]);
            }
        }

        $this->command->info('✅ Users with roles created successfully!');
        $this->command->info('Login Credentials:');
        $this->command->info('Super Admin: superadmin@test.com / password');
        $this->command->info('Admin: admin@test.com / password');
        $this->command->info('Operation Manager: opmanager@test.com / password');
        $this->command->info('Team Leader 1: teamlead1@test.com / password');
        $this->command->info('Team Leader 2: teamlead2@test.com / password');
        $this->command->info('Stack Leader 1: stacklead1@test.com / password');
        $this->command->info('Stack Leader 2: stacklead2@test.com / password');
        $this->command->info('Team Member 1: dev1@test.com / password');
        $this->command->info('Team Member 2: dev2@test.com / password');
        $this->command->info('Team Member 3: dev3@test.com / password');
        $this->command->info('Team Member 4: designer1@test.com / password');
        $this->command->info('Team Member 5: designer2@test.com / password');
        $this->command->newLine();
        $this->command->info('Designation users (password: password):');
        $this->command->info('  GM: tonmoy@gmail.com');
        $this->command->info('  OP: jb@gmail.com');
        $this->command->info('  PM: Saifur@gmail.com');
        $this->command->info('  T.L: tarek@gmail.com');
        $this->command->info('  Stack Leader: helal@gmail.com');
        $this->command->info('  Team Member: emon@gmail.com');
    }
}
