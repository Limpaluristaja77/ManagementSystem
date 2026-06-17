<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.approve',
            'attendance.update-approved',
            'attendance.deactivate',

            'users.view',
            'users.create',
            'users.deactivate',

            'roles.view',
            'roles.create',
            'roles.deactivate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $superadmin->syncPermissions($permissions);

        $manager->syncPermissions([
            'attendance.view',
            'attendance.update',
            'attendance.approve',
            'attendance.update-approved',
        ]);

        $user->syncPermissions([
            'attendance.view',
            'attendance.create',
            'attendance.update',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
