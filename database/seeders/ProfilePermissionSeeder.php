<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProfilePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view profiles',
            'manage profiles',
            'create profile',
            'edit profile',
            'delete profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $operationManagerRole = Role::where('name', 'operation-manager')->first();
        $projectManagerRole = Role::where('name', 'project-manager')->first();
        $stackLeaderRole = Role::where('name', 'stack-leader')->first();
        $teamLeaderRole = Role::where('name', 'team-leader')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        if ($operationManagerRole) {
            $operationManagerRole->givePermissionTo([
                'view profiles',
                'manage profiles',
                'create profile',
                'edit profile',
                'delete profile',
            ]);
        }

        if ($projectManagerRole) {
            $projectManagerRole->givePermissionTo([
                'view profiles',
                'manage profiles',
                'create profile',
                'edit profile',
                'delete profile',
            ]);
        }

        if ($stackLeaderRole) {
            $stackLeaderRole->givePermissionTo([
                'view profiles',
            ]);
        }

        if ($teamLeaderRole) {
            $teamLeaderRole->givePermissionTo([
                'view profiles',
            ]);
        }
    }
}
