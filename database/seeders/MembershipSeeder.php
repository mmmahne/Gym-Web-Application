<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'user')->first();

        Membership::create([
            'user_id' => $user->id,
            'type' => 'basic',
            'price' => 50.00,
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'is_active' => true,
        ]);
    }
} 