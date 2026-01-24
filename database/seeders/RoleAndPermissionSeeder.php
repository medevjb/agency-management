<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // User management
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',
            // Order management
            'manage orders',
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            // Task management
            'manage tasks',
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            // Project management
            'manage all projects',
            'view all projects',
            'manage team projects',
            'view team projects',
            'edit stack projects',
            'view assigned projects',
            'edit assigned tasks',
            // Team management
            'manage teams',
            'view teams',
            'manage team members',
            // Tech stack management
            'manage tech stacks',
            'view tech stacks',
            'manage tech stack members',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles and Assign Permissions (using kebab-case)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $operationManager = Role::firstOrCreate(['name' => 'operation-manager']);
        $operationManager->givePermissionTo([
            'manage users', 'view users', 'create users', 'edit users', 'delete users',
            'manage orders', 'view orders', 'create orders', 'edit orders', 'delete orders',
            'manage tasks', 'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'manage all projects', 'view all projects',
            'manage teams', 'view teams', 'manage team members',
            'manage tech stacks', 'view tech stacks', 'manage tech stack members',
        ]);

        $teamLeader = Role::firstOrCreate(['name' => 'team-leader']);
        $teamLeader->givePermissionTo([
            'manage team projects', 'view team projects',
            'view teams', 'manage team members',
        ]);

        $stackLeader = Role::firstOrCreate(['name' => 'stack-leader']);
        $stackLeader->givePermissionTo([
            'view team projects', 'edit stack projects',
            'view tech stacks', 'manage tech stack members',
        ]);

        $teamMember = Role::firstOrCreate(['name' => 'team-member']);
        $teamMember->givePermissionTo([
            'view assigned projects', 'edit assigned tasks',
            'view teams', 'view tech stacks',
        ]);

        // Create additional roles with permissions
        $projectManager = Role::firstOrCreate(['name' => 'project-manager']);
        $projectManager->givePermissionTo([
            'view users',
            'manage orders', 'view orders', 'create orders', 'edit orders', 'delete orders',
            'manage tasks', 'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
            'manage all projects', 'view all projects',
            'manage team projects', 'view team projects',
            'manage teams', 'view teams', 'manage team members',
        ]);

        $developer = Role::firstOrCreate(['name' => 'developer']);
        $developer->givePermissionTo([
            'view users',
            'view orders',
            'view tasks',
            'view all projects',
            'view team projects',
            'edit assigned tasks',
            'view teams', 'view tech stacks',
        ]);

        $designer = Role::firstOrCreate(['name' => 'designer']);
        $designer->givePermissionTo([
            'view users',
            'view orders',
            'view tasks',
            'view all projects',
            'view team projects',
            'edit assigned tasks',
            'view teams', 'view tech stacks',
        ]);

        $qa = Role::firstOrCreate(['name' => 'qa']);
        $qa->givePermissionTo([
            'view users',
            'view orders',
            'view tasks',
            'view all projects',
            'view team projects',
            'edit assigned tasks',
            'view teams', 'view tech stacks',
        ]);

        // 3. Create a default Admin user for testing
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'employee_id' => 'EMP0001',
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        )->assignRole('admin');
    }
}
