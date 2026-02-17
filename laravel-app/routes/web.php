<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDepartmentController;
use App\Http\Controllers\AdminShiftController;
use App\Http\Controllers\AdminHolidayController;
use App\Http\Controllers\AdminLeaveTypeController;
use App\Http\Controllers\AdminAuditLogController;
use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\AdminExportController;
use App\Http\Controllers\AdminBackupController;
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
    Route::post('/izin', [IzinController::class, 'store'])
        ->name('izin.store');

    // ADMIN ONLY
    Route::middleware(AdminOnly::class)->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/admin/attendance', [App\Http\Controllers\AdminAttendanceController::class, 'index'])
            ->name('admin.attendance');

        Route::get('/admin/absence-management', [App\Http\Controllers\AdminAbsenceController::class, 'index'])
            ->name('admin.absence.index');

        Route::patch('/admin/absence-management/{izin}/status', [App\Http\Controllers\AdminAbsenceController::class, 'updateStatus'])
            ->name('admin.absence.updateStatus');
            
        Route::resource('employees', EmployeeController::class);

        // SETTINGS
        Route::get('/admin/settings', [App\Http\Controllers\AdminSystemSettingController::class, 'index'])
            ->name('admin.settings.index');

        Route::get('/admin/settings/category/{category}', [App\Http\Controllers\AdminSystemSettingController::class, 'index'])
            ->whereIn('category', ['umum', 'kehadiran', 'organisasi', 'data'])
            ->name('admin.settings.category');
        
        Route::patch('/admin/settings/work-hours', [App\Http\Controllers\AdminSystemSettingController::class, 'updateWorkHours'])
            ->name('admin.settings.work-hours.update');

        Route::post('/admin/settings/work-hours/reset', [App\Http\Controllers\AdminSystemSettingController::class, 'resetWorkHours'])
            ->name('admin.settings.work-hours.reset');

        Route::patch('/admin/settings/location', [App\Http\Controllers\AdminSystemSettingController::class, 'updateLocation'])
            ->name('admin.settings.location.update');

        Route::post('/admin/settings/location/reset', [App\Http\Controllers\AdminSystemSettingController::class, 'resetLocation'])
            ->name('admin.settings.location.reset');

        Route::patch('/admin/settings/company-profile', [App\Http\Controllers\AdminSystemSettingController::class, 'updateCompanyProfile'])
            ->name('admin.settings.company-profile.update');
        Route::post('/admin/settings/company-profile/reset', [App\Http\Controllers\AdminSystemSettingController::class, 'resetCompanyProfile'])
            ->name('admin.settings.company-profile.reset');

        Route::patch('/admin/settings/attendance-policy', [App\Http\Controllers\AdminSystemSettingController::class, 'updateAttendancePolicy'])
            ->name('admin.settings.attendance-policy.update');
        Route::post('/admin/settings/attendance-policy/reset', [App\Http\Controllers\AdminSystemSettingController::class, 'resetAttendancePolicy'])
            ->name('admin.settings.attendance-policy.reset');

        Route::patch('/admin/settings/face-recognition', [App\Http\Controllers\AdminSystemSettingController::class, 'updateFaceRecognition'])
            ->name('admin.settings.face-recognition.update');
        Route::post('/admin/settings/face-recognition/reset', [App\Http\Controllers\AdminSystemSettingController::class, 'resetFaceRecognition'])
            ->name('admin.settings.face-recognition.reset');
        Route::post('/admin/settings/face-recognition/test', [App\Http\Controllers\AdminSystemSettingController::class, 'testFaceService'])
            ->name('admin.settings.face-recognition.test');

        Route::post('/admin/settings/departments', [AdminDepartmentController::class, 'store'])
            ->name('admin.settings.departments.store');
        Route::patch('/admin/settings/departments/{department}', [AdminDepartmentController::class, 'update'])
            ->name('admin.settings.departments.update');
        Route::delete('/admin/settings/departments/{department}', [AdminDepartmentController::class, 'destroy'])
            ->name('admin.settings.departments.destroy');

        // SHIFTS
        Route::post('/admin/settings/shifts', [AdminShiftController::class, 'store'])
            ->name('admin.settings.shifts.store');
        Route::patch('/admin/settings/shifts/{shift}', [AdminShiftController::class, 'update'])
            ->name('admin.settings.shifts.update');
        Route::delete('/admin/settings/shifts/{shift}', [AdminShiftController::class, 'destroy'])
            ->name('admin.settings.shifts.destroy');

        // HOLIDAYS
        Route::post('/admin/settings/holidays/import', [AdminHolidayController::class, 'import'])
            ->name('admin.settings.holidays.import');
        Route::post('/admin/settings/holidays', [AdminHolidayController::class, 'store'])
            ->name('admin.settings.holidays.store');
        Route::patch('/admin/settings/holidays/{holiday}', [AdminHolidayController::class, 'update'])
            ->name('admin.settings.holidays.update');
        Route::delete('/admin/settings/holidays/{holiday}', [AdminHolidayController::class, 'destroy'])
            ->name('admin.settings.holidays.destroy');

        // LEAVE TYPES
        Route::post('/admin/settings/leave-types', [AdminLeaveTypeController::class, 'store'])
            ->name('admin.settings.leave-types.store');
        Route::patch('/admin/settings/leave-types/{leaveType}', [AdminLeaveTypeController::class, 'update'])
            ->name('admin.settings.leave-types.update');
        Route::delete('/admin/settings/leave-types/{leaveType}', [AdminLeaveTypeController::class, 'destroy'])
            ->name('admin.settings.leave-types.destroy');

        Route::patch('/admin/settings/notifications', [App\Http\Controllers\AdminSystemSettingController::class, 'updateNotificationSettings'])
            ->name('admin.settings.notifications.update');

        // AUDIT LOGS
        Route::get('/admin/audit-logs', [AdminAuditLogController::class, 'index'])
            ->name('admin.audit-logs.index');

        // ANNOUNCEMENTS
        Route::resource('admin/announcements', AdminAnnouncementController::class)
            ->names('admin.announcements');

        // RETENTION
        Route::patch('/admin/settings/retention', [App\Http\Controllers\AdminSystemSettingController::class, 'updateRetention'])
            ->name('admin.settings.backup-config.update');

        // EXPORT
        Route::get('/admin/export/attendance', [AdminExportController::class, 'exportAttendance'])
            ->name('admin.export.attendance');
        Route::get('/admin/export/employees', [AdminExportController::class, 'exportEmployees'])
            ->name('admin.export.employees');

        // BACKUPS
        Route::post('/admin/backups', [AdminBackupController::class, 'store'])
            ->name('admin.backups.create');
        Route::delete('/admin/backups/{filename}', [AdminBackupController::class, 'destroy'])
            ->name('admin.backups.destroy');
        Route::get('/admin/backups/{filename}', [AdminBackupController::class, 'download'])
            ->name('admin.backups.download');
        Route::post('/admin/cleanup', [AdminBackupController::class, 'cleanup'])
            ->name('admin.cleanup');
    });
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
