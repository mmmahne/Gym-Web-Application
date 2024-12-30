<?php

namespace App\Http\Requests\GymClass;

use Illuminate\Foundation\Http\FormRequest;

class StoreGymClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'trainer' || $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:yoga,cardio,strength,hiit',
            'max_capacity' => 'required|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|between:1,7',
            'price' => 'required|numeric|min:0',
        ];
    }
} 