<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; // Added for the /user route
use App\Http\Controllers\Api\AttendanceController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Face Registration Route (Session based auth for now since it's called from blade with CSRF)
Route::post('/face/register', [\App\Http\Controllers\Api\FaceRegistrationController::class, 'register'])
    ->middleware('web'); // Enable session & CSRF for this specific internal API call if not using Sanctum

Route::post('/attendance/auto', [AttendanceController::class, 'autoAttendance']);
