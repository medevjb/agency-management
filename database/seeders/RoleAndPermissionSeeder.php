<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions grouped by module
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage team members',

            // Order Management
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'view order amounts',

            // Task Management
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'edit task status',
            'submit tasks',
            'view assigned tasks',

            // Team Management
            'view teams',
            'create teams',
            'edit teams',
            'delete teams',
            'manage teams',
            'view all teams',

            // Tech Stack Management
            'view tech stacks',
            'create tech stacks',
            'edit tech stacks',
            'delete tech stacks',
            'manage tech stack members',

            // Profiles
            'view profiles',
            'create profile',
            'edit profile',
            'delete profile',
            'manage profiles',

            // Freelance Profiles
            'view freelance profiles',
            'create freelance profile',
            'edit freelance profile',
            'delete freelance profile',

            // Bug Reports
            'view bug reports',
            'create bug reports',
            'edit bug reports',
            'delete bug reports',
            'manage bug reports',

            // Portfolio
            'view any portfolio',
            'view own portfolio',
            'create portfolio',
            'edit any portfolio',
            'edit own portfolio',
            'delete any portfolio',
            'delete own portfolio',
            'feature portfolio',

            // Dashboard & Settings
            'view dashboard',
            'view team dashboard',
            'manage settings',

            // Sales
            'view sales dashboard',
            'view own sales orders',
            'view team sales orders',
            'create sales orders',
            'edit sales orders',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Permissions

        // ADMIN — Full access to everything
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // OPERATION MANAGER — Everything except admin settings / roles management
        $operationManager = Role::firstOrCreate(['name' => 'operation-manager']);
        $operationManager->syncPermissions([
            'view users', 'create users', 'edit users', 'delete users', 'manage team members',
            'view orders', 'create orders', 'edit orders', 'delete orders', 'view order amounts',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'edit task status', 'submit tasks', 'view assigned tasks',
            'view teams', 'create teams', 'edit teams', 'delete teams', 'manage teams', 'view all teams',
            'view tech stacks', 'create tech stacks', 'edit tech stacks', 'delete tech stacks', 'manage tech stack members',
            'view profiles', 'create profile', 'edit profile', 'delete profile', 'manage profiles',
            'view freelance profiles', 'create freelance profile', 'edit freelance profile', 'delete freelance profile',
            'view bug reports', 'create bug reports', 'edit bug reports', 'delete bug reports', 'manage bug reports',
            'view any portfolio', 'view own portfolio', 'create portfolio', 'edit any portfolio', 'edit own portfolio', 'delete any portfolio', 'delete own portfolio', 'feature portfolio',
            'view dashboard', 'view team dashboard',
        ]);

        // PROJECT MANAGER — Same as Operation Manager
        $projectManager = Role::firstOrCreate(['name' => 'project-manager']);
        $projectManager->syncPermissions([
            'view users', 'create users', 'edit users', 'delete users', 'manage team members',
            'view orders', 'create orders', 'edit orders', 'delete orders', 'view order amounts',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'edit task status', 'submit tasks', 'view assigned tasks',
            'view teams', 'create teams', 'edit teams', 'delete teams', 'manage teams', 'view all teams',
            'view tech stacks', 'create tech stacks', 'edit tech stacks', 'delete tech stacks', 'manage tech stack members',
            'view profiles', 'create profile', 'edit profile', 'delete profile', 'manage profiles',
            'view freelance profiles', 'create freelance profile', 'edit freelance profile', 'delete freelance profile',
            'view bug reports', 'create bug reports', 'edit bug reports', 'delete bug reports', 'manage bug reports',
            'view any portfolio', 'view own portfolio', 'create portfolio', 'edit any portfolio', 'edit own portfolio', 'delete any portfolio', 'delete own portfolio', 'feature portfolio',
            'view dashboard', 'view team dashboard',
        ]);

        // TEAM LEADER — Manage own team, create orders/tasks for team, see team revenue
        $teamLeader = Role::firstOrCreate(['name' => 'team-leader']);
        $teamLeader->syncPermissions([
            'manage team members', 'create users',
            'view orders', 'create orders', 'edit orders', 'delete orders', 'view order amounts',
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'edit task status', 'submit tasks',
            'view teams', 'edit teams',
            'view tech stacks', 'create tech stacks', 'edit tech stacks', 'delete tech stacks', 'manage tech stack members',
            'view profiles', 'view freelance profiles',
            'view bug reports', 'create bug reports',
            'view any portfolio', 'view own portfolio', 'create portfolio', 'edit own portfolio', 'delete own portfolio',
            'view dashboard', 'view team dashboard',
        ]);

        // STACK LEADER — Manage own tech stack, read team data, create tasks for stack members
        $stackLeader = Role::firstOrCreate(['name' => 'stack-leader']);
        $stackLeader->syncPermissions([
            'view orders', 'view order amounts',
            'view tasks', 'create tasks', 'edit tasks', 'edit task status', 'submit tasks',
            'view teams',
            'view tech stacks', 'edit tech stacks', 'manage tech stack members',
            'view profiles', 'view freelance profiles',
            'view bug reports', 'create bug reports',
            'view any portfolio', 'view own portfolio', 'create portfolio', 'edit own portfolio', 'delete own portfolio',
            'view dashboard', 'view team dashboard',
        ]);

        // TEAM MEMBER — View team data, see all orders (with badge), manage own tasks
        $teamMember = Role::firstOrCreate(['name' => 'team-member']);
        $teamMember->syncPermissions([
            'view orders', 'view order amounts',
            'view tasks', 'create tasks', 'edit tasks', 'submit tasks', 'edit task status',
            'view teams',
            'view tech stacks',
            'view profiles', 'view freelance profiles',
            'view bug reports', 'create bug reports',
            'view own portfolio', 'create portfolio', 'edit own portfolio', 'delete own portfolio',
            'view dashboard', 'view team dashboard',
        ]);

        // SALES LEADER — Owns a group of sales members; sees every order sold by
        // that group. No access to operational team, member or delivery data.
        $salesLeader = Role::firstOrCreate(['name' => 'sales-leader']);
        $salesLeader->syncPermissions([
            'view orders', 'create orders', 'edit orders', 'view order amounts',
            'view own sales orders', 'view team sales orders', 'create sales orders', 'edit sales orders',
            'view sales dashboard',
            'view tasks', 'create tasks', 'edit tasks', 'edit task status', 'submit tasks', 'view assigned tasks',
            'view profiles', 'view freelance profiles',
            'view bug reports', 'create bug reports',
            'view own portfolio', 'create portfolio', 'edit own portfolio', 'delete own portfolio',
        ]);

        // SALES MEMBER — Sells under one sales leader; sees only their own orders.
        $salesMember = Role::firstOrCreate(['name' => 'sales-member']);
        $salesMember->syncPermissions([
            'view orders', 'create orders', 'edit orders', 'view order amounts',
            'view own sales orders', 'create sales orders', 'edit sales orders',
            'view sales dashboard',
            'view tasks', 'create tasks', 'edit tasks', 'edit task status', 'submit tasks', 'view assigned tasks',
            'view profiles', 'view freelance profiles',
            'view bug reports', 'create bug reports',
            'view own portfolio', 'create portfolio', 'edit own portfolio', 'delete own portfolio',
        ]);

        // 3. Migrate old roles to team-member
        $oldRoles = ['developer', 'designer', 'qa'];
        foreach ($oldRoles as $oldRoleName) {
            $oldRole = Role::where('name', $oldRoleName)->first();
            if ($oldRole) {
                $usersWithOldRole = User::role($oldRoleName)->get();
                foreach ($usersWithOldRole as $user) {
                    $user->removeRole($oldRoleName);
                    if (! $user->hasRole('team-member')) {
                        $user->assignRole('team-member');
                    }
                }
                $oldRole->delete();
            }
        }

        // 4. Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@pmt.com'],
            [
                'employee_id' => 'ADMIN001',
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        )->assignRole('admin');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
