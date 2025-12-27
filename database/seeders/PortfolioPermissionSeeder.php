<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PortfolioPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view any portfolio',
            'view own portfolio',
            'create portfolio',
            'edit own portfolio',
            'edit any portfolio',
            'delete own portfolio',
            'delete any portfolio',
            'publish portfolio',
            'feature portfolio',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $operationManagerRole = Role::where('name', 'operation-manager')->first();
        $stackLeaderRole = Role::where('name', 'stack-leader')->first();
        $teamLeaderRole = Role::where('name', 'team-leader')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        if ($operationManagerRole) {
            $operationManagerRole->givePermissionTo([
                'view any portfolio',
                'view own portfolio',
                'create portfolio',
                'edit own portfolio',
                'edit any portfolio',
                'delete own portfolio',
                'delete any portfolio',
                'publish portfolio',
                'feature portfolio',
            ]);
        }

        if ($stackLeaderRole) {
            $stackLeaderRole->givePermissionTo([
                'view any portfolio',
                'view own portfolio',
                'create portfolio',
                'edit own portfolio',
                'delete own portfolio',
                'publish portfolio',
            ]);
        }

        if ($teamLeaderRole) {
            $teamLeaderRole->givePermissionTo([
                'view any portfolio',
                'view own portfolio',
                'create portfolio',
                'edit own portfolio',
                'delete own portfolio',
                'publish portfolio',
            ]);
        }
    }
}
