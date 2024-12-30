<?php

namespace Tests\Feature\GymClass;

use App\Models\GymClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GymClassTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Remove the seeder since we want to control the test data
        // $this->seed();
    }

    public function test_trainer_can_create_class()
    {
        $trainer = User::factory()->create([
            'role' => 'trainer',
            'password' => bcrypt('password')
        ]);

        $response = $this->actingAs($trainer)->postJson('/api/classes', [
            'name' => 'Morning Yoga',
            'description' => 'Start your day with relaxing yoga',
            'type' => 'yoga',
            'max_capacity' => 20,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'days_of_week' => [1, 3, 5],
            'price' => 15.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'trainer_id',
                    'type',
                    'max_capacity',
                    'start_time',
                    'end_time',
                    'days_of_week',
                    'price',
                    'is_active',
                ]
            ]);
    }

    public function test_user_can_view_class_schedule()
    {
        // Create a trainer first
        $trainer = User::factory()->create(['role' => 'trainer']);
        
        // Create classes with the trainer
        GymClass::factory()->count(3)->create([
            'trainer_id' => $trainer->id,
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'role' => 'user',
            'password' => bcrypt('password')
        ]);

        $response = $this->actingAs($user)->getJson('/api/classes/schedule');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_regular_user_cannot_create_class()
    {
        $user = User::factory()->create([
            'role' => 'user',
            'password' => bcrypt('password')
        ]);

        $response = $this->actingAs($user)->postJson('/api/classes', [
            'name' => 'Morning Yoga',
            'description' => 'Start your day with relaxing yoga',
            'type' => 'yoga',
            'max_capacity' => 20,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'days_of_week' => [1, 3, 5],
            'price' => 15.00,
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_view_schedule()
    {
        $response = $this->getJson('/api/classes/schedule');
        $response->assertStatus(401);
    }
} 