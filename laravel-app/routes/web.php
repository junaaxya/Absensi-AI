<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminEmployeeFaceController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Middleware\AdminOnly;


Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| ROUTE WAJIB LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // DASHBOARD KARYAWAN
    Route::get('/dashboard', [AttendanceController::class, 'dashboard'])
        ->name('dashboard');


    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/profile/password', [PasswordController::class, 'edit'])
        ->name('password.edit');

    Route::patch('/profile/password', [PasswordController::class, 'update'])
        ->name('password.update');


    // PENGAJUAN KETIDAKHADIRAN
    Route::get('/izin', [IzinController::class, 'index'])
        ->name('izin.index');

    Route::post('/izin', [IzinController::class, 'store'])
        ->name('izin.store');

    // ADMIN ONLY
    Route::middleware(AdminOnly::class)->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/admin/attendance', [App\Http\Controllers\AdminAttendanceController::class, 'index'])
            ->name('admin.attendance');

        Route::get('/admin/attendance/index', [App\Http\Controllers\AdminAttendanceController::class, 'index'])
            ->name('admin.attendance.index');

        Route::get('/admin/absence-management', [App\Http\Controllers\AdminAbsenceController::class, 'index'])
            ->name('admin.absence.index');

        Route::get('/admin/izin', [App\Http\Controllers\AdminAbsenceController::class, 'index'])
            ->name('admin.izin.index');

        Route::patch('/admin/izin/{izin}/approve', [IzinController::class, 'approve'])
            ->name('admin.izin.approve');

        Route::patch('/admin/izin/{izin}/reject', [IzinController::class, 'reject'])
            ->name('admin.izin.reject');

        Route::patch('/admin/absence-management/{izin}/status', [App\Http\Controllers\AdminAbsenceController::class, 'updateStatus'])
            ->name('admin.absence.updateStatus');
            
        Route::resource('employees', EmployeeController::class);

        Route::get('/admin/employees/{employee}/face-data', [AdminEmployeeFaceController::class, 'show'])
            ->name('admin.employees.face-data.show');

        Route::delete('/admin/employees/{employee}/face-data', [AdminEmployeeFaceController::class, 'destroy'])
            ->name('admin.employees.face-data.destroy');

        Route::delete('/admin/employees/{employee}/face-data/photos/{photo}', [AdminEmployeeFaceController::class, 'destroyPhoto'])
            ->where('photo', '.*')
            ->name('admin.employees.face-data.photos.destroy');

        // SETTINGS
        Route::get('/admin/settings', [App\Http\Controllers\AdminSystemSettingController::class, 'index'])
            ->name('admin.settings.index');
        
        Route::patch('/admin/settings/work-hours', [App\Http\Controllers\AdminSystemSettingController::class, 'updateWorkHours'])
            ->name('admin.settings.work-hours.update');

        Route::post('/admin/settings/work-hours/reset', [App\Http\Controllers\AdminSystemSettingController::class, 'resetWorkHours'])
            ->name('admin.settings.work-hours.reset');

        Route::patch('/admin/settings/location', [App\Http\Controllers\AdminSystemSettingController::class, 'updateLocation'])
            ->name('admin.settings.location.update');

        Route::post('/admin/settings/location/reset', [App\Http\Controllers\AdminSystemSettingController::class, 'resetLocation'])
            ->name('admin.settings.location.reset');

    });
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
