<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gym.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create trainer user
        User::create([
            'name' => 'Trainer User',
            'email' => 'trainer@gym.com',
            'password' => Hash::make('password'),
            'role' => 'trainer',
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@gym.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create additional users with 'user' role
        User::factory(5)->create();
    }
} 