<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $memberships = auth()->user()->hasRole('admin')
            ? Membership::with('user')->get()
            : auth()->user()->memberships;

        return $this->success($memberships);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return $this->error('Unauthorized access', 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:basic,premium,vip',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $validated['is_active'] = true;
        $membership = Membership::create($validated);

        return $this->success($membership, 'Membership created successfully', 201);
    }

    public function show(Membership $membership)
    {
        if (!auth()->user()->hasRole('admin') && auth()->id() !== $membership->user_id) {
            return $this->error('Unauthorized access', 403);
        }

        return $this->success($membership->load('user'));
    }

    public function update(Request $request, Membership $membership)
    {
        if (!auth()->user()->hasRole('admin')) {
            return $this->error('Unauthorized access', 403);
        }

        $validated = $request->validate([
            'type' => 'sometimes|in:basic,premium,vip',
            'price' => 'sometimes|numeric|min:0',
            'end_date' => 'sometimes|date|after:start_date',
            'is_active' => 'sometimes|boolean',
            'payment_status' => 'sometimes|in:pending,paid,failed',
        ]);

        $membership->update($validated);

        return $this->success($membership, 'Membership updated successfully');
    }

    public function active()
    {
        $membership = auth()->user()->memberships()
            ->where('is_active', true)
            ->where('end_date', '>', now())
            ->where('payment_status', 'paid')
            ->latest()
            ->first();

        if (!$membership) {
            return $this->error('No active membership found', 404);
        }

        return $this->success($membership);
    }
} 