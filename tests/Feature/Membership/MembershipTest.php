<?php

namespace Tests\Feature\Membership;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_membership()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($admin)->postJson('/api/memberships', [
            'user_id' => $user->id,
            'type' => 'premium',
            'price' => 99.99,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'payment_status' => 'paid',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'type',
                    'price',
                    'start_date',
                    'end_date',
                    'is_active',
                    'payment_status',
                ]
            ]);
    }

    public function test_user_can_view_active_membership()
    {
        $user = User::factory()->create(['role' => 'user']);
        $membership = Membership::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
            'payment_status' => 'paid',
            'end_date' => now()->addDays(30),
        ]);

        $response = $this->actingAs($user)->getJson('/api/memberships/active');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $membership->id,
                    'user_id' => $user->id,
                ]
            ]);
    }

    public function test_regular_user_cannot_create_membership()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->postJson('/api/memberships', [
            'user_id' => $user->id,
            'type' => 'premium',
            'price' => 99.99,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonth()->format('Y-m-d'),
            'payment_status' => 'paid',
        ]);

        $response->assertStatus(403);
    }
} 