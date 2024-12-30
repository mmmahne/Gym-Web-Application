<?php

namespace Tests\Feature\Attendance;

use App\Models\Booking;
use App\Models\GymClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_trainer_can_mark_attendance()
    {
        $trainer = User::factory()->create(['role' => 'trainer']);
        $user = User::factory()->create(['role' => 'user']);
        $class = GymClass::factory()->create(['trainer_id' => $trainer->id]);
        
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'booking_date' => now(),
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($trainer)->postJson("/api/attendance/bookings/{$booking->id}", [
            'attended' => true
        ]);

        $response->assertStatus(200);
        $this->assertTrue($booking->fresh()->attended);
        $this->assertEquals('completed', $booking->fresh()->status);
    }

    public function test_trainer_can_view_class_attendance()
    {
        $trainer = User::factory()->create(['role' => 'trainer']);
        $class = GymClass::factory()->create(['trainer_id' => $trainer->id]);
        $now = now();
        
        Booking::factory()->count(3)->create([
            'class_id' => $class->id,
            'booking_date' => $now,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($trainer)->getJson("/api/attendance/class?date={$now->toDateString()}&class_id={$class->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_view_their_attendance()
    {
        $user = User::factory()->create(['role' => 'user']);
        Booking::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'attended' => true
        ]);

        $response = $this->actingAs($user)->getJson('/api/attendance/my');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
} 