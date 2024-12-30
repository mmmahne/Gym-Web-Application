<?php

namespace Database\Factories;

use App\Models\GymClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'class_id' => GymClass::factory(),
            'booking_date' => fake()->dateTimeBetween('now', '+1 week'),
            'status' => 'confirmed',
            'attended' => false,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled'
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'attended' => true,
            'booking_date' => fake()->dateTimeBetween('-1 week', 'now')
        ]);
    }
} 