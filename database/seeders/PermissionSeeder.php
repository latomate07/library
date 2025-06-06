<?php
// database/seeders/PermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view authors',
            'create authors',
            'update authors',
            'delete authors',
            'view books',
            'create books',
            'update books',
            'delete books',
            'manage library',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $librarian = Role::create(['name' => 'librarian']);
        $librarian->givePermissionTo([
            'view authors',
            'create authors',
            'update authors',
            'view books',
            'create books',
            'update books',
            'manage library',
        ]);

        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view authors',
            'view books',
        ]);
    }
}