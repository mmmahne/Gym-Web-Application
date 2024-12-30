<?php

namespace Tests\Feature\Stats;

use App\Models\Booking;
use App\Models\GymClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard_stats()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(5)->create(['role' => 'user']);
        User::factory()->count(2)->create(['role' => 'trainer']);
        
        $classes = GymClass::factory()->count(3)->create();
        Booking::factory()->count(10)->create([
            'class_id' => $classes->random()->id,
        ]);

        $response = $this->actingAs($admin)->getJson('/api/stats/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_users',
                    'total_trainers',
                    'total_classes',
                    'active_bookings',
                    'class_attendance',
                    'popular_classes',
                    'monthly_bookings',
                ]
            ]);
    }

    public function test_user_can_view_their_stats()
    {
        $user = User::factory()->create(['role' => 'user']);
        $classes = GymClass::factory()->count(3)->create();
        
        Booking::factory()->count(5)->create([
            'user_id' => $user->id,
            'class_id' => $classes->random()->id,
            'attended' => true,
        ]);

        $response = $this->actingAs($user)->getJson('/api/stats/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_bookings',
                    'attended_classes',
                    'class_history',
                    'favorite_classes',
                ]
            ]);
    }

    public function test_regular_user_cannot_view_dashboard_stats()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->getJson('/api/stats/dashboard');

        $response->assertStatus(403);
    }
} 