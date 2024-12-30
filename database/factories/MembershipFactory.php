<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['basic', 'premium', 'vip']),
            'price' => fake()->randomFloat(2, 30, 200),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
            'payment_status' => 'paid',
        ];
    }
} 