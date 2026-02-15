<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\IzinController;
use App\Http\Middleware\EnsureUserHasRole;

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

    // PENGAJUAN KETIDAKHADIRAN (User)
    Route::post('/izin', [IzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/riwayat', [IzinController::class, 'index'])->name('izin.index');

    // ADMIN ROUTES
    Route::middleware([EnsureUserHasRole::class . ':admin'])->prefix('admin')->group(function () {
        // Manajemen Karyawan
        Route::resource('employees', \App\Http\Controllers\EmployeeController::class);

        // Laporan Absensi
        Route::get('/attendance', [AttendanceController::class, 'adminIndex'])->name('admin.attendance.index');

        // Approval Izin
        Route::get('/izin', [IzinController::class, 'adminIndex'])->name('admin.izin.index');
        Route::patch('/izin/{izin}/approve', [IzinController::class, 'approve'])->name('admin.izin.approve');
        Route::patch('/izin/{izin}/reject', [IzinController::class, 'reject'])->name('admin.izin.reject');
    });
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
