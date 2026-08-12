<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Adds only the two sales roles and the permissions they need.
 *
 * Safe to run on a live database: unlike RoleAndPermissionSeeder it never
 * touches the existing roles, so any permissions tuned in the admin UI survive.
 */
class SalesRoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $salesPermissions = [
            'view sales dashboard',
            'view own sales orders',
            'view team sales orders',
            'create sales orders',
            'edit sales orders',
        ];

        // Permissions the sales roles share with the rest of the app.
        $sharedPermissions = [
            'view orders', 'create orders', 'edit orders', 'view order amounts',
            'view tasks', 'create tasks', 'edit tasks', 'edit task status', 'submit tasks', 'view assigned tasks',
            'view profiles', 'view freelance profiles',
            'view bug reports', 'create bug reports',
            'view own portfolio', 'create portfolio', 'edit own portfolio', 'delete own portfolio',
        ];

        foreach (array_merge($salesPermissions, $sharedPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $leader = Role::firstOrCreate(['name' => 'sales-leader', 'guard_name' => 'web']);
        $leader->givePermissionTo(array_merge($sharedPermissions, $salesPermissions));

        $member = Role::firstOrCreate(['name' => 'sales-member', 'guard_name' => 'web']);
        $member->givePermissionTo(array_merge($sharedPermissions, array_diff($salesPermissions, ['view team sales orders'])));

        // Admin keeps full access.
        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo(Permission::all());
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('✅ sales-leader and sales-member are ready.');
    }
}
