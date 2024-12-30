<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gym_classes';

    protected $fillable = [
        'name',
        'description',
        'trainer_id',
        'type',
        'max_capacity',
        'start_time',
        'end_time',
        'days_of_week',
        'price',
        'is_active'
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'class_id');
    }
} 