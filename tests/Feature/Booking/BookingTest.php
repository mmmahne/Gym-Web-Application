<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\GymClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_book_class()
    {
        $trainer = User::factory()->create(['role' => 'trainer']);
        $user = User::factory()->create(['role' => 'user']);
        $class = GymClass::factory()->create([
            'trainer_id' => $trainer->id,
            'max_capacity' => 20
        ]);

        $response = $this->actingAs($user)->postJson('/api/bookings', [
            'class_id' => $class->id,
            'booking_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'class_id',
                    'booking_date',
                    'status',
                    'attended',
                ]
            ]);
    }

    public function test_user_cannot_book_full_class()
    {
        $trainer = User::factory()->create(['role' => 'trainer']);
        $class = GymClass::factory()->create([
            'trainer_id' => $trainer->id,
            'max_capacity' => 1
        ]);

        // Create a booking that fills the class
        $existingUser = User::factory()->create(['role' => 'user']);
        Booking::factory()->create([
            'user_id' => $existingUser->id,
            'class_id' => $class->id,
            'booking_date' => now()->addDays(1),
            'status' => 'confirmed'
        ]);

        // Try to book with another user
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->postJson('/api/bookings', [
            'class_id' => $class->id,
            'booking_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_cancel_booking()
    {
        $user = User::factory()->create(['role' => 'user']);
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'booking_date' => now()->addDays(1),
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($user)->postJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(200);
        $this->assertEquals('cancelled', $booking->fresh()->status);
    }
} 