<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use ApiResponse;

    public function markAttendance(Request $request, Booking $booking)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        if ($booking->booking_date->toDateString() !== now()->toDateString()) {
            return $this->error('Attendance can only be marked for today\'s bookings', 422);
        }

        $validated = $request->validate([
            'attended' => 'required|boolean',
        ]);

        $booking->update([
            'attended' => $validated['attended'],
            'status' => 'completed'
        ]);

        return $this->success($booking->load('user'), 'Attendance marked successfully');
    }

    public function getAttendance(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:gym_classes,id'
        ]);

        $bookings = Booking::with(['user', 'gymClass'])
            ->where('class_id', $validated['class_id'])
            ->whereDate('booking_date', $validated['date'])
            ->get();

        return $this->success($bookings);
    }

    public function myAttendance()
    {
        $bookings = auth()->user()->bookings()
            ->with('gymClass')
            ->where('status', 'completed')
            ->orderBy('booking_date', 'desc')
            ->get();

        return $this->success($bookings);
    }
} 