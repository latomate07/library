<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AuthorSeeder;
use Database\Seeders\BookSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            AuthorSeeder::class,
            BookSeeder::class,
        ]);
    }
}
