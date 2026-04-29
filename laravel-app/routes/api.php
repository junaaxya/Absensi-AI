<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SystemSettingController;
use App\Http\Controllers\Api\FaceRegistrationController;
use App\Http\Controllers\Api\VisitAttendanceController;
use App\Models\CompanyBranch;

// Session-based auth: 'web' (session+CSRF) + 'auth' (login required)
Route::middleware(['web', 'auth'])->group(function () {

    Route::get('/user', fn (Request $request) => $request->user());

    Route::post('/face/register', [FaceRegistrationController::class, 'register']);

    Route::post('/attendance/auto', [AttendanceController::class, 'autoAttendance'])
        ->middleware('throttle:6,1');

    Route::put('/admin/settings', [SystemSettingController::class, 'update'])
        ->middleware('permission:manage_system_settings');

    Route::post('/visit/check-in', [VisitAttendanceController::class, 'checkIn']);
    Route::post('/visit/{visitAttendance}/check-out', [VisitAttendanceController::class, 'checkOut']);
    Route::post('/visit/{visitAttendance}/track', [VisitAttendanceController::class, 'trackLocation']);

    Route::get('/companies/{company}/branches', function ($companyId) {
        return CompanyBranch::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
    });
});
