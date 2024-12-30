<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GymClassController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\CheckInController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    
    // Class routes
    Route::get('classes/schedule', [GymClassController::class, 'schedule']);
    Route::apiResource('classes', GymClassController::class);

    // Booking routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']);
        Route::post('/', [BookingController::class, 'store']);
        Route::get('/my', [BookingController::class, 'myBookings']);
        Route::get('/{booking}', [BookingController::class, 'show']);
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel']);
    });

    // Membership routes
    Route::prefix('memberships')->group(function () {
        Route::get('/', [MembershipController::class, 'index']);
        Route::post('/', [MembershipController::class, 'store']);
        Route::get('/active', [MembershipController::class, 'active']);
        Route::get('/{membership}', [MembershipController::class, 'show']);
        Route::put('/{membership}', [MembershipController::class, 'update']);
    });

    // Attendance routes
    Route::prefix('attendance')->group(function () {
        Route::post('/bookings/{booking}', [AttendanceController::class, 'markAttendance']);
        Route::get('/class', [AttendanceController::class, 'getAttendance']);
        Route::get('/my', [AttendanceController::class, 'myAttendance']);
    });

    // Stats routes
    Route::prefix('stats')->group(function () {
        Route::get('/dashboard', [StatsController::class, 'dashboard']);
        Route::get('/user', [StatsController::class, 'userStats']);
    });

    // Check-in routes
    Route::prefix('check-in')->group(function () {
        Route::post('/', [CheckInController::class, 'checkIn']);
        Route::post('/class/{booking}', [CheckInController::class, 'checkInToClass']);
        Route::get('/history', [CheckInController::class, 'history']);
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });
});

// In public routes section
Route::prefix('password')->group(function () {
    Route::post('/forgot', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset', [PasswordResetController::class, 'reset']);
}); 