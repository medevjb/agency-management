<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        $permissions = [
            // Admin Dashboard & System Settings (Admin ONLY)
            'access admin dashboard',
            'manage system settings',

            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',
            'manage user permissions',

            // Team Management
            'view teams',
            'create teams',
            'edit teams',
            'delete teams',
            'manage team members',
            'view all teams', // View teams other than own

            // Tech Stack Management
            'view tech stacks',
            'create tech stacks',
            'edit tech stacks',
            'delete tech stacks',
            'manage stack members',
            'view all tech stacks', // View stacks other than own
            'manage own stack', // For stack-leader to manage their stack

            // Order Management
            'view all orders',
            'view own team orders',
            'create orders',
            'edit orders',
            'delete orders',
            'manage order status',
            'manage own team orders', // For team-leader
            'view stack orders', // For stack-leader
            'manage stack orders', // For stack-leader

            // Task Management
            'view all tasks',
            'view own team tasks',
            'create tasks',
            'edit own tasks', // Edit own tasks only
            'edit all tasks', // Edit any task
            'delete tasks',
            'manage task status',
            'manage own task status', // Update status of own tasks
            'create own tasks', // Create tasks for self

            // Dashboard & Analytics
            'view dashboard',
            'view order dashboard',
            'view analytics',
            'view revenue reports',

            // Bug Reports
            'view bug reports',
            'create bug reports',
            'edit bug reports',
            'delete bug reports',
            'resolve bug reports',

            // Portfolio & Profile
            'view portfolios',
            'manage own portfolio',
            'view profiles',
            'manage own profile',

            // Notifications
            'view notifications',
            'send notifications',
            'manage notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ Created '.count($permissions).' permissions');

        // Create roles and assign permissions

        // 1. ADMIN - Full access to everything
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());
        $this->command->info('✅ Configured Admin role with all permissions');

        // 2. OPERATION MANAGER - Like admin but NO system settings/admin dashboard
        $operationManager = Role::firstOrCreate(['name' => 'operation-manager', 'guard_name' => 'web']);
        $operationManager->givePermissionTo([
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',
            'manage user permissions',

            // Team Management
            'view teams',
            'view all teams',
            'create teams',
            'edit teams',
            'delete teams',
            'manage team members',

            // Tech Stack Management
            'view tech stacks',
            'view all tech stacks',
            'create tech stacks',
            'edit tech stacks',
            'delete tech stacks',
            'manage stack members',

            // Order Management
            'view all orders',
            'create orders',
            'edit orders',
            'delete orders',
            'manage order status',

            // Task Management
            'view all tasks',
            'create tasks',
            'edit all tasks',
            'delete tasks',
            'manage task status',

            // Dashboard & Analytics
            'view dashboard',
            'view order dashboard',
            'view analytics',
            'view revenue reports',

            // Bug Reports
            'view bug reports',
            'create bug reports',
            'edit bug reports',
            'delete bug reports',
            'resolve bug reports',

            // Portfolio & Profile
            'view portfolios',
            'view profiles',

            // Notifications
            'view notifications',
            'send notifications',
            'manage notifications',
        ]);
        $this->command->info('✅ Configured Operation Manager role');

        // 3. PROJECT MANAGER - Same as Operation Manager
        $projectManager = Role::firstOrCreate(['name' => 'project-manager', 'guard_name' => 'web']);
        $projectManager->givePermissionTo($operationManager->permissions);
        $this->command->info('✅ Configured Project Manager role');

        // 4. TEAM LEADER - Own team data + manage team + manage team's tech stack
        $teamLeader = Role::firstOrCreate(['name' => 'team-leader', 'guard_name' => 'web']);
        $teamLeader->givePermissionTo([
            // View own team data only
            'view own team orders',
            'view own team tasks',
            'view teams',

            // Manage own team
            'manage team members',
            'edit teams', // Edit own team

            // Manage own team's tech stack
            'view tech stacks',
            'manage own stack',

            // Order Management (own team only)
            'create orders',
            'manage own team orders',
            'manage order status',

            // Task Management (own team)
            'view tasks',
            'create tasks',
            'edit all tasks', // Can edit team tasks
            'manage task status',

            // Dashboard
            'view dashboard',
            'view order dashboard',

            // Bug Reports
            'view bug reports',
            'create bug reports',
            'edit bug reports', // Edit own bug reports
            'resolve bug reports', // Resolve team bug reports

            // Profile
            'view profiles',
            'manage own profile',

            // Notifications
            'view notifications',
        ]);
        $this->command->info('✅ Configured Team Leader role');

        // 5. STACK LEADER - View all teams + manage own tech stack
        $stackLeader = Role::firstOrCreate(['name' => 'stack-leader', 'guard_name' => 'web']);
        $stackLeader->givePermissionTo([
            // View all teams data (read-only)
            'view all teams',
            'view teams',
            'view tech stacks',
            'view all tech stacks',

            // Manage own tech stack
            'manage own stack',
            'manage stack members',
            'edit tech stacks', // Edit own stack

            // View all orders/tasks using their stack (read-only)
            'view all orders',
            'view all tasks',
            'view stack orders',
            'manage stack orders', // Can manage orders using their stack

            // Dashboard
            'view dashboard',
            'view order dashboard',

            // Bug Reports (view all, create for their stack)
            'view bug reports',
            'create bug reports',
            'edit bug reports', // Edit own
            'resolve bug reports', // Resolve stack-related

            // Profile
            'view profiles',
            'manage own profile',

            // Notifications
            'view notifications',
        ]);
        $this->command->info('✅ Configured Stack Leader role');

        // 6. TEAM MEMBER - View team data + manage own tasks only
        $teamMember = Role::firstOrCreate(['name' => 'team-member', 'guard_name' => 'web']);
        $teamMember->givePermissionTo([
            // View own team data (read-only)
            'view own team orders',
            'view own team tasks',
            'view teams',
            'view tech stacks',

            // Task Management - OWN tasks only
            'view tasks',
            'create own tasks',
            'edit own tasks',
            'manage own task status',

            // Dashboard
            'view dashboard',
            'view order dashboard',

            // Bug Reports
            'view bug reports',
            'create bug reports',
            'edit bug reports', // Edit own

            // Profile
            'view profiles',
            'manage own profile',

            // Notifications
            'view notifications',
        ]);
        $this->command->info('✅ Configured Team Member role');

        $this->command->newLine();
        $this->command->info('✅ All roles and permissions configured successfully!');
        $this->command->newLine();
        $this->command->comment('Summary:');
        $this->command->comment('• Admin: Full access + system settings');
        $this->command->comment('• Operation Manager: Full access except system settings');
        $this->command->comment('• Project Manager: Full access except system settings');
        $this->command->comment('• Team Leader: Own team data + manage team & team stack');
        $this->command->comment('• Stack Leader: View all teams + manage own tech stack');
        $this->command->comment('• Team Member: View team data + manage own tasks');
    }
}
