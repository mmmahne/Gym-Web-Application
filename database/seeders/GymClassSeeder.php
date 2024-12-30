<?php

namespace Database\Seeders;

use App\Models\GymClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class GymClassSeeder extends Seeder
{
    public function run(): void
    {
        $trainer = User::where('role', 'trainer')->first();

        GymClass::create([
            'name' => 'Morning Yoga',
            'description' => 'Start your day with relaxing yoga',
            'trainer_id' => $trainer->id,
            'type' => 'yoga',
            'max_capacity' => 20,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'days_of_week' => [1, 3, 5], // Monday, Wednesday, Friday
            'price' => 15.00,
            'is_active' => true,
        ]);

        GymClass::create([
            'name' => 'Evening HIIT',
            'description' => 'High-intensity interval training',
            'trainer_id' => $trainer->id,
            'type' => 'hiit',
            'max_capacity' => 15,
            'start_time' => '18:00',
            'end_time' => '19:00',
            'days_of_week' => [2, 4], // Tuesday, Thursday
            'price' => 20.00,
            'is_active' => true,
        ]);
    }
} 