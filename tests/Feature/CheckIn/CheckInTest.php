<?php

namespace Tests\Feature\CheckIn;

use App\Models\User;
use App\Models\Membership;
use App\Models\Booking;
use App\Models\GymClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_check_in_with_active_membership()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $membership = Membership::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
            'end_date' => now()->addDays(30),
        ]);

        $response = $this->actingAs($user)->postJson('/api/check-in');

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'status' => 'completed',
            'attended' => true,
            'class_id' => null
        ]);
    }

    public function test_user_can_check_in_to_booked_class()
    {
        $user = User::factory()->create(['role' => 'user']);
        $class = GymClass::factory()->create();
        
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'booking_date' => now(),
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($user)->postJson("/api/check-in/class/{$booking->id}");

        $response->assertStatus(200);
        $this->assertTrue($booking->fresh()->attended);
        $this->assertNotNull($booking->fresh()->check_in_time);
    }

    public function test_cannot_check_in_without_active_membership()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->postJson('/api/check-in');

        $response->assertStatus(422);
    }

    public function test_admin_can_view_check_in_history()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $bookings = Booking::factory()->count(5)->create([
            'attended' => true,
            'check_in_time' => now(),
            'status' => 'completed'
        ]);

        $response = $this->actingAs($admin)->getJson('/api/check-in/history');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }
} 