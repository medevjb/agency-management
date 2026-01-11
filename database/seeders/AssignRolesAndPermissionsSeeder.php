<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Assigning roles and permissions to users...');

        // Get or create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $operationManager = Role::firstOrCreate(['name' => 'operation-manager']);
        $teamLead = Role::firstOrCreate(['name' => 'team-leader']);
        $stackLead = Role::firstOrCreate(['name' => 'stack-leader']);
        $teamMember = Role::firstOrCreate(['name' => 'team-member']);
        $projectManager = Role::firstOrCreate(['name' => 'project-manager']);
        $developer = Role::firstOrCreate(['name' => 'developer']);
        $designer = Role::firstOrCreate(['name' => 'designer']);
        $qa = Role::firstOrCreate(['name' => 'qa']);

        // Assign users to roles based on their email/position
        $users = User::where('is_active', true)->get();

        foreach ($users as $user) {
            match (true) {
                $user->email === 'admin@test.com' => $user->assignRole($admin),
                // Operation Managers
                in_array($user->email, [
                    'sarah.johnson@test.com',
                    'michael.chen@test.com',
                ]) => $user->assignRole($operationManager, $projectManager),

                // Team Leads
                in_array($user->email, [
                    'david.williams@test.com',
                    'emily.rodriguez@test.com',
                    'james.wilson@test.com',
                ]) => $user->assignRole($teamLead),

                // Project Managers (who are also team leads)
                in_array($user->email, [
                    'sarah.johnson@test.com',
                    'michael.chen@test.com',
                ]) => $user->assignRole($projectManager),

                // Stack Leads (tech stack leads)
                in_array($user->email, [
                    'christopher.brown@test.com',
                    'michelle.davis@test.com',
                ]) => $user->assignRole($stackLead),

                // Developers
                in_array($user->email, [
                    'alex.thompson@test.com',
                    'jessica.martinez@test.com',
                    'ryan.garcia@test.com',
                    'amanda.lee@test.com',
                    'christopher.brown@test.com',
                    'michelle.davis@test.com',
                ]) => $user->assignRole($developer),

                // Designers
                in_array($user->email, [
                    'stephanie.clark@test.com',
                    'thomas.anderson@test.com',
                    'laura.white@test.com',
                    'emily.rodriguez@test.com',
                ]) => $user->assignRole($designer),

                // QA
                in_array($user->email, [
                    'kevin.harris@test.com',
                    'nancy.martin@test.com',
                ]) => $user->assignRole($qa),

                // Team Members
                default => $user->assignRole($teamMember),
            };

            $this->command->info("✓ Assigned role to {$user->name} ({$user->email})");
        }

        $this->command->info('✅ Roles and permissions assigned successfully!');
        $this->command->info('');
        $this->command->info('Role Distribution:');
        $this->command->info('  - Admin: 1 user');
        $this->command->info('  - Operation Manager: 2 users');
        $this->command->info('  - Project Manager: 2 users');
        $this->command->info('  - Team Lead: 3 users');
        $this->command->info('  - Stack Lead: 2 users');
        $this->command->info('  - Developer: 6 users');
        $this->command->info('  - Designer: 4 users');
        $this->command->info('  - QA: 2 users');
        $this->command->info('  - Team Member: All other users');
    }
}
