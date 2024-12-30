<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GymClass;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $bookings = auth()->user()->hasRole(['admin', 'trainer']) 
            ? Booking::with(['user', 'gymClass'])->get()
            : auth()->user()->bookings()->with('gymClass')->get();

        return $this->success($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:gym_classes,id',
            'booking_date' => 'required|date|after:now',
        ]);

        $gymClass = GymClass::findOrFail($validated['class_id']);

        // Check if class is full
        $bookingsCount = $gymClass->bookings()
            ->where('booking_date', $validated['booking_date'])
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($bookingsCount >= $gymClass->max_capacity) {
            return $this->error('Class is full for this date', 422);
        }

        // Check if user already has a booking for this class on this date
        $existingBooking = auth()->user()->bookings()
            ->where('class_id', $validated['class_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingBooking) {
            return $this->error('You already have a booking for this class on this date', 422);
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'class_id' => $validated['class_id'],
            'booking_date' => $validated['booking_date'],
            'status' => 'confirmed',
            'attended' => false,
        ]);

        return $this->success($booking->load('gymClass'), 'Booking created successfully', 201);
    }

    public function show(Booking $booking)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer']) && auth()->id() !== $booking->user_id) {
            return $this->error('Unauthorized access', 403);
        }

        return $this->success($booking->load(['user', 'gymClass']));
    }

    public function cancel(Booking $booking)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer']) && auth()->id() !== $booking->user_id) {
            return $this->error('Unauthorized access', 403);
        }

        if ($booking->status === 'cancelled') {
            return $this->error('Booking is already cancelled', 422);
        }

        if ($booking->booking_date < now()) {
            return $this->error('Cannot cancel past bookings', 422);
        }

        $booking->update(['status' => 'cancelled']);
        return $this->success($booking, 'Booking cancelled successfully');
    }

    public function myBookings()
    {
        $bookings = auth()->user()->bookings()
            ->with('gymClass')
            ->orderBy('booking_date', 'desc')
            ->get();

        return $this->success($bookings);
    }
} 