<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@library.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create librarian user
        $librarian = User::create([
            'name' => 'Librarian User',
            'email' => 'librarian@library.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $librarian->assignRole('librarian');

        // Create viewer user
        $viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@library.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $viewer->assignRole('viewer');
    }
}