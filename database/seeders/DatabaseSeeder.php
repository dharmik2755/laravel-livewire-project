<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            ['name' => 'Manager', 'password' => Hash::make('password'), 'role' => 'manager']
        );

        User::updateOrCreate(
            ['email' => 'developer1@example.com'],
            ['name' => 'Developer One', 'password' => Hash::make('password'), 'role' => 'developer']
        );

        User::updateOrCreate(
            ['email' => 'developer2@example.com'],
            ['name' => 'Developer Two', 'password' => Hash::make('password'), 'role' => 'developer']
        );
    }
}
