<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedInitialData extends Command
{
    protected $signature = 'gym:seed-initial-data';
    protected $description = 'Seed initial data for the gym management system';

    public function handle()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gym.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->info('Initial data seeded successfully');
    }
} 