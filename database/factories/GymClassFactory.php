<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'trainer_id' => User::factory()->create(['role' => 'trainer'])->id,
            'type' => fake()->randomElement(['yoga', 'cardio', 'strength', 'hiit']),
            'max_capacity' => fake()->numberBetween(5, 30),
            'start_time' => fake()->time(),
            'end_time' => fake()->time(),
            'days_of_week' => [1, 3, 5], // Mon, Wed, Fri
            'price' => fake()->randomFloat(2, 10, 100),
            'is_active' => true,
        ];
    }
} 