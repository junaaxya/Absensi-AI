<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminEmployeeFaceController;
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
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminAbsenceController;
use App\Http\Controllers\AdminSystemSettingController;


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

        // DASHBOARD - accessible to ALL admin-level roles (no extra permission needed)
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        // ATTENDANCE & ABSENCE VIEWING - requires at least team-level viewing
        Route::middleware(['permission:view_team_attendance'])->group(function () {
            Route::get('/admin/attendance', [AdminAttendanceController::class, 'index'])
                ->name('admin.attendance');
            Route::get('/admin/attendance/index', [AdminAttendanceController::class, 'index'])
                ->name('admin.attendance.index');
            Route::get('/admin/absence-management', [AdminAbsenceController::class, 'index'])
                ->name('admin.absence.index');
            Route::get('/admin/izin', [AdminAbsenceController::class, 'index'])
                ->name('admin.izin.index');
        });

        // IZIN APPROVAL - requires at least team-level approval
        Route::middleware(['permission:approve_team_izin'])->group(function () {
            Route::patch('/admin/izin/{izin}/approve', [IzinController::class, 'approve'])
                ->name('admin.izin.approve');
            Route::patch('/admin/izin/{izin}/reject', [IzinController::class, 'reject'])
                ->name('admin.izin.reject');
            Route::patch('/admin/absence-management/{izin}/status', [AdminAbsenceController::class, 'updateStatus'])
                ->name('admin.absence.updateStatus');
        });

        // EMPLOYEE MANAGEMENT - requires manage_employees permission
        Route::middleware(['permission:manage_employees'])->group(function () {
            Route::resource('employees', EmployeeController::class);
        });

        // FACE DATA MANAGEMENT - requires manage_face_data permission
        Route::middleware(['permission:manage_face_data'])->group(function () {
            Route::get('/admin/employees/{employee}/face-data', [AdminEmployeeFaceController::class, 'show'])
                ->name('admin.employees.face-data.show');
            Route::delete('/admin/employees/{employee}/face-data', [AdminEmployeeFaceController::class, 'destroy'])
                ->name('admin.employees.face-data.destroy');
            Route::delete('/admin/employees/{employee}/face-data/photos/{photo}', [AdminEmployeeFaceController::class, 'destroyPhoto'])
                ->where('photo', '.*')
                ->name('admin.employees.face-data.photos.destroy');
        });

        // SYSTEM SETTINGS - requires manage_system_settings (Direktur only)
        Route::middleware(['permission:manage_system_settings'])->group(function () {
            Route::get('/admin/settings', [AdminSystemSettingController::class, 'index'])
                ->name('admin.settings.index');
            Route::get('/admin/settings/category/{category}', [AdminSystemSettingController::class, 'index'])
                ->whereIn('category', ['umum', 'kehadiran', 'organisasi', 'data'])
                ->name('admin.settings.category');
            Route::patch('/admin/settings/work-hours', [AdminSystemSettingController::class, 'updateWorkHours'])
                ->name('admin.settings.work-hours.update');
            Route::post('/admin/settings/work-hours/reset', [AdminSystemSettingController::class, 'resetWorkHours'])
                ->name('admin.settings.work-hours.reset');
            Route::patch('/admin/settings/location', [AdminSystemSettingController::class, 'updateLocation'])
                ->name('admin.settings.location.update');
            Route::post('/admin/settings/location/reset', [AdminSystemSettingController::class, 'resetLocation'])
                ->name('admin.settings.location.reset');
            Route::patch('/admin/settings/company-profile', [AdminSystemSettingController::class, 'updateCompanyProfile'])
                ->name('admin.settings.company-profile.update');
            Route::post('/admin/settings/company-profile/reset', [AdminSystemSettingController::class, 'resetCompanyProfile'])
                ->name('admin.settings.company-profile.reset');
            Route::patch('/admin/settings/attendance-policy', [AdminSystemSettingController::class, 'updateAttendancePolicy'])
                ->name('admin.settings.attendance-policy.update');
            Route::post('/admin/settings/attendance-policy/reset', [AdminSystemSettingController::class, 'resetAttendancePolicy'])
                ->name('admin.settings.attendance-policy.reset');
            Route::patch('/admin/settings/face-recognition', [AdminSystemSettingController::class, 'updateFaceRecognition'])
                ->name('admin.settings.face-recognition.update');
            Route::post('/admin/settings/face-recognition/reset', [AdminSystemSettingController::class, 'resetFaceRecognition'])
                ->name('admin.settings.face-recognition.reset');
            Route::post('/admin/settings/face-recognition/test', [AdminSystemSettingController::class, 'testFaceService'])
                ->name('admin.settings.face-recognition.test');
            Route::patch('/admin/settings/notifications', [AdminSystemSettingController::class, 'updateNotificationSettings'])
                ->name('admin.settings.notifications.update');
            Route::patch('/admin/settings/retention', [AdminSystemSettingController::class, 'updateRetention'])
                ->name('admin.settings.backup-config.update');
        });

        // DEPARTMENT MANAGEMENT - requires manage_departments
        Route::middleware(['permission:manage_departments'])->group(function () {
            Route::post('/admin/settings/departments', [AdminDepartmentController::class, 'store'])
                ->name('admin.settings.departments.store');
            Route::patch('/admin/settings/departments/{department}', [AdminDepartmentController::class, 'update'])
                ->name('admin.settings.departments.update');
            Route::delete('/admin/settings/departments/{department}', [AdminDepartmentController::class, 'destroy'])
                ->name('admin.settings.departments.destroy');
        });

        // SHIFT MANAGEMENT - requires manage_shifts
        Route::middleware(['permission:manage_shifts'])->group(function () {
            Route::post('/admin/settings/shifts', [AdminShiftController::class, 'store'])
                ->name('admin.settings.shifts.store');
            Route::patch('/admin/settings/shifts/{shift}', [AdminShiftController::class, 'update'])
                ->name('admin.settings.shifts.update');
            Route::delete('/admin/settings/shifts/{shift}', [AdminShiftController::class, 'destroy'])
                ->name('admin.settings.shifts.destroy');
        });

        // HOLIDAY MANAGEMENT - requires manage_holidays
        Route::middleware(['permission:manage_holidays'])->group(function () {
            Route::post('/admin/settings/holidays/import', [AdminHolidayController::class, 'import'])
                ->name('admin.settings.holidays.import');
            Route::post('/admin/settings/holidays', [AdminHolidayController::class, 'store'])
                ->name('admin.settings.holidays.store');
            Route::patch('/admin/settings/holidays/{holiday}', [AdminHolidayController::class, 'update'])
                ->name('admin.settings.holidays.update');
            Route::delete('/admin/settings/holidays/{holiday}', [AdminHolidayController::class, 'destroy'])
                ->name('admin.settings.holidays.destroy');
        });

        // LEAVE TYPE MANAGEMENT - requires manage_leave_types
        Route::middleware(['permission:manage_leave_types'])->group(function () {
            Route::post('/admin/settings/leave-types', [AdminLeaveTypeController::class, 'store'])
                ->name('admin.settings.leave-types.store');
            Route::patch('/admin/settings/leave-types/{leaveType}', [AdminLeaveTypeController::class, 'update'])
                ->name('admin.settings.leave-types.update');
            Route::delete('/admin/settings/leave-types/{leaveType}', [AdminLeaveTypeController::class, 'destroy'])
                ->name('admin.settings.leave-types.destroy');
        });

        // AUDIT LOGS - requires view_audit_logs
        Route::middleware(['permission:view_audit_logs'])->group(function () {
            Route::get('/admin/audit-logs', [AdminAuditLogController::class, 'index'])
                ->name('admin.audit-logs.index');
        });

        // ANNOUNCEMENTS - requires manage_announcements
        Route::middleware(['permission:manage_announcements'])->group(function () {
            Route::resource('admin/announcements', AdminAnnouncementController::class)
                ->names('admin.announcements');
        });

        // EXPORT - requires export_data
        Route::middleware(['permission:export_data'])->group(function () {
            Route::get('/admin/export/attendance', [AdminExportController::class, 'exportAttendance'])
                ->name('admin.export.attendance');
            Route::get('/admin/export/employees', [AdminExportController::class, 'exportEmployees'])
                ->name('admin.export.employees');
        });

        // BACKUPS - requires manage_backups (Direktur only)
        Route::middleware(['permission:manage_backups'])->group(function () {
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
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
