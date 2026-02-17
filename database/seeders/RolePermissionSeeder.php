<?php

declare(strict_types=1);

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
            'appointments.view',
            'appointments.manage',
            'catalog.manage',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::query()->firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $master = Role::query()->firstOrCreate(['name' => 'master', 'guard_name' => 'web']);
        $client = Role::query()->firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        $admin->syncPermissions($permissions);
        $manager->syncPermissions(['appointments.view', 'appointments.manage', 'catalog.manage']);
        $master->syncPermissions(['appointments.view']);
        $client->syncPermissions([]);
    }
}
