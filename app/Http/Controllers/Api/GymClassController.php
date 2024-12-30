<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GymClass\StoreGymClassRequest;
use App\Models\GymClass;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GymClassController extends Controller
{
    use ApiResponse;

    public function index()
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        $classes = GymClass::with('trainer')->get();
        return $this->success($classes);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:yoga,cardio,strength,hiit',
            'max_capacity' => 'required|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|between:1,7',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['trainer_id'] = auth()->id();
        $validated['is_active'] = true;
        $class = GymClass::create($validated);

        return $this->success($class, 'Class created successfully', 201);
    }

    public function show(GymClass $class)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        return $this->success($class->load('trainer'));
    }

    public function update(Request $request, GymClass $class)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:yoga,cardio,strength,hiit',
            'max_capacity' => 'sometimes|integer|min:1',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'days_of_week' => 'sometimes|array',
            'days_of_week.*' => 'integer|between:1,7',
            'price' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $class->update($validated);
        return $this->success($class, 'Class updated successfully');
    }

    public function destroy(GymClass $class)
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        $class->delete();
        return $this->success(null, 'Class deleted successfully');
    }

    public function schedule()
    {
        if (!auth()->user()->hasRole('user')) {
            return $this->error('Unauthorized access', 403);
        }

        $classes = GymClass::with('trainer')
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
            
        return $this->success($classes);
    }
} 