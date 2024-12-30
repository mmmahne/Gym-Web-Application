<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    use ApiResponse;

    public function checkIn()
    {
        $user = auth()->user();

        // Check if user has active membership
        $activeMembership = $user->memberships()
            ->where('is_active', true)
            ->where('end_date', '>=', now())
            ->first();

        if (!$activeMembership) {
            return $this->error('No active membership found', 422);
        }

        // Create general check-in record
        $checkIn = Booking::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'attended' => true,
            'check_in_time' => now(),
            'booking_date' => now(),
            'class_id' => null  // Allow null for general gym access
        ]);

        return $this->success([
            'membership' => $activeMembership,
            'check_in' => $checkIn
        ], 'Check-in successful');
    }

    public function checkInToClass(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return $this->error('Unauthorized access', 403);
        }

        if ($booking->status !== 'confirmed') {
            return $this->error('Booking is not confirmed', 422);
        }

        if (!$booking->booking_date->isToday()) {
            return $this->error('Booking is not for today', 422);
        }

        $booking->update([
            'attended' => true,
            'status' => 'completed',
            'check_in_time' => now()
        ]);

        return $this->success($booking, 'Class check-in successful');
    }

    public function history(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return $this->error('Unauthorized access', 403);
        }

        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $query = Booking::with(['user', 'gymClass'])
            ->where('attended', true)
            ->whereNotNull('check_in_time');

        if (isset($validated['from_date'])) {
            $query->whereDate('check_in_time', '>=', $validated['from_date']);
        }

        if (isset($validated['to_date'])) {
            $query->whereDate('check_in_time', '<=', $validated['to_date']);
        }

        if (isset($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        $checkIns = $query->orderByDesc('check_in_time')->get();

        return $this->success($checkIns);
    }
} 