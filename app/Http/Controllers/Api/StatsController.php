<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GymClass;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    use ApiResponse;

    public function dashboard()
    {
        if (!auth()->user()->hasRole(['admin', 'trainer'])) {
            return $this->error('Unauthorized access', 403);
        }

        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_trainers' => User::where('role', 'trainer')->count(),
            'total_classes' => GymClass::count(),
            'active_bookings' => Booking::where('status', 'confirmed')->count(),
            'class_attendance' => $this->getClassAttendance(),
            'popular_classes' => $this->getPopularClasses(),
            'monthly_bookings' => $this->getMonthlyBookings(),
        ];

        return $this->success($stats);
    }

    public function userStats()
    {
        $userId = auth()->id();
        
        $stats = [
            'total_bookings' => Booking::where('user_id', $userId)->count(),
            'attended_classes' => Booking::where('user_id', $userId)
                ->where('attended', true)
                ->count(),
            'class_history' => $this->getUserClassHistory($userId),
            'favorite_classes' => $this->getUserFavoriteClasses($userId),
        ];

        return $this->success($stats);
    }

    private function getClassAttendance()
    {
        return Booking::select('class_id')
            ->selectRaw('COUNT(*) as total_bookings')
            ->selectRaw('SUM(CASE WHEN attended = 1 THEN 1 ELSE 0 END) as attended')
            ->groupBy('class_id')
            ->with('gymClass:id,name')
            ->get();
    }

    private function getPopularClasses()
    {
        return GymClass::select([
            'gym_classes.id',
            'gym_classes.name',
            'gym_classes.description',
            'gym_classes.type',
            'gym_classes.trainer_id',
            'gym_classes.max_capacity',
            'gym_classes.created_at',
            'gym_classes.updated_at'
        ])
            ->join('bookings', 'gym_classes.id', '=', 'bookings.class_id')
            ->selectRaw('COUNT(bookings.id) as booking_count')
            ->groupBy([
                'gym_classes.id',
                'gym_classes.name',
                'gym_classes.description',
                'gym_classes.type',
                'gym_classes.trainer_id',
                'gym_classes.max_capacity',
                'gym_classes.created_at',
                'gym_classes.updated_at'
            ])
            ->orderByDesc('booking_count')
            ->limit(5)
            ->get();
    }

    private function getMonthlyBookings()
    {
        return Booking::select(DB::raw('DATE_FORMAT(booking_date, "%Y-%m") as month'))
            ->selectRaw('COUNT(*) as total')
            ->where('booking_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getUserClassHistory($userId)
    {
        return Booking::where('user_id', $userId)
            ->with('gymClass:id,name,type')
            ->orderByDesc('booking_date')
            ->limit(10)
            ->get();
    }

    private function getUserFavoriteClasses($userId)
    {
        return GymClass::select([
            'gym_classes.id',
            'gym_classes.name',
            'gym_classes.description',
            'gym_classes.type',
            'gym_classes.trainer_id',
            'gym_classes.max_capacity',
            'gym_classes.created_at',
            'gym_classes.updated_at'
        ])
            ->join('bookings', 'gym_classes.id', '=', 'bookings.class_id')
            ->where('bookings.user_id', $userId)
            ->selectRaw('COUNT(bookings.id) as booking_count')
            ->groupBy([
                'gym_classes.id',
                'gym_classes.name',
                'gym_classes.description',
                'gym_classes.type',
                'gym_classes.trainer_id',
                'gym_classes.max_capacity',
                'gym_classes.created_at',
                'gym_classes.updated_at'
            ])
            ->orderByDesc('booking_count')
            ->limit(3)
            ->get();
    }
} 