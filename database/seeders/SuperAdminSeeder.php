<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create the admin user
        $admin = User::where('email', 'admin@admin.com')->first();

        if (! $admin) {
            $this->command->error('Admin user (admin@admin.com) not found!');

            return;
        }

        // Create admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );

        // Define all permissions for the application
        $permissions = [
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

            // Tech Stack Management
            'view tech stacks',
            'create tech stacks',
            'edit tech stacks',
            'delete tech stacks',

            // Order Management
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'manage order status',
            'view order analytics',
            'manage order assignments',

            // Task Management
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'manage task status',
            'assign tasks',
            'view archived tasks',
            'archive tasks',
            'bulk update tasks',

            // Project Management
            'view projects',
            'create projects',
            'edit projects',
            'delete projects',
            'manage project members',

            // Bug Report Management
            'view bug reports',
            'create bug reports',
            'edit bug reports',
            'delete bug reports',
            'resolve bug reports',

            // Settings Management
            'view settings',
            'manage settings',
            'manage system settings',
            'manage project statuses',
            'manage order statuses',

            // Reports & Analytics
            'view reports',
            'view analytics',
            'export data',
            'view revenue reports',
            'view performance reports',

            // Notifications
            'view notifications',
            'send notifications',
            'manage notifications',

            // Portfolio & Profile
            'view portfolios',
            'manage own portfolio',
            'view profiles',
            'manage own profile',

            // Full Access
            'full access',
            'super admin',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Get all permissions
        $allPermissions = Permission::all();

        // Give super-admin role all permissions
        $superAdminRole->givePermissionTo($allPermissions);

        // Assign super-admin role to admin user
        $admin->syncRoles([$superAdminRole->id]);
        $admin->syncPermissions($allPermissions);

        $this->command->info('✅ Super Admin setup completed successfully!');
        $this->command->newLine();
        $this->command->info('User: '.$admin->name.' ('.$admin->email.')');
        $this->command->info('Role: admin');
        $this->command->info('Total Permissions: '.$allPermissions->count());
        $this->command->newLine();
        $this->command->comment('The admin user now has full access to all features!');
    }
}
