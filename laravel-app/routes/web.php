<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\IzinController;

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
        

    // ABSENSI
    Route::post('/absen-masuk', [AttendanceController::class, 'absenMasuk'])
        ->name('absen.masuk');

    Route::post('/absen-keluar', [AttendanceController::class, 'absenKeluar'])
        ->name('absen.keluar');

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
    Route::post('/izin', [IzinController::class, 'store'])
        ->name('izin.store');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
