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
use App\Http\Controllers\AdminViolationController;
use App\Http\Controllers\AdminAnomalyController;
use App\Http\Controllers\AdminPayrollController;
use App\Http\Controllers\AdminVisitAttendanceController;
use App\Http\Controllers\AdminLeaveBalanceController;
use App\Http\Controllers\AdminProjectController;
use App\Http\Controllers\AdminTaskController;
use App\Http\Controllers\AdminAssetController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AdminFormController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\AdminTrainingController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\AdminRecruitmentController;
use App\Http\Controllers\AdminSalaryComponentController;
use App\Http\Controllers\AdminRateManagementController;
use App\Http\Controllers\AdminPayrollTemplateController;
use App\Http\Controllers\ViolationHistoryController;
use App\Http\Controllers\MyAssetController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\MyTaskController;


Route::get('/', function () {
    return redirect()->route('login');
});

// Pending approval page (accessible by authenticated but unapproved users)
Route::middleware(['auth'])->get('/pending-approval', function () {
    if (auth()->user()->is_approved) {
        return redirect()->route('dashboard');
    }
    return view('auth.pending-approval');
})->name('pending-approval');

/*
|--------------------------------------------------------------------------
| ROUTE WAJIB LOGIN + APPROVED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'approved'])->group(function () {

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

    Route::get('/profile/photo', [ProfileController::class, 'photo'])
        ->name('profile.photo');

    Route::get('/profile/password', [PasswordController::class, 'edit'])
        ->name('password.edit');

    Route::patch('/profile/password', [PasswordController::class, 'update'])
        ->name('password.update');


    // PENGAJUAN KETIDAKHADIRAN
    Route::get('/izin', [IzinController::class, 'index'])
        ->name('izin.index');

    Route::post('/izin', [IzinController::class, 'store'])
        ->name('izin.store');

    Route::get('/izin/balance', [IzinController::class, 'getLeaveBalance'])
        ->name('izin.balance');

    Route::post('/izin/{izin}/wfa-checkin', [IzinController::class, 'wfaCheckin'])
        ->name('izin.wfa-checkin');

    // TIKET LAYANAN (user-facing)
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/comment', [TicketController::class, 'addComment'])->name('tickets.comment');

    // FORM INTERNAL (user-facing)
    Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
    Route::get('/forms/my-submissions', [FormController::class, 'mySubmissions'])->name('forms.submissions');
    Route::get('/forms/{template}', [FormController::class, 'show'])->name('forms.show');
    Route::post('/forms/{template}', [FormController::class, 'store'])->name('forms.store');
    Route::get('/forms/submissions/{submission}', [FormController::class, 'showSubmission'])->name('forms.submission.show');
    Route::post('/forms/submissions/{submission}/comment', [FormController::class, 'addComment'])->name('forms.submission.comment');

    // RIWAYAT PELANGGARAN (user-facing)
    Route::get('/my-violations', [ViolationHistoryController::class, 'index'])->name('violations.index');

    // ASET SAYA (user-facing)
    Route::get('/my-assets', [MyAssetController::class, 'index'])->name('my-assets.index');

    // TRAINING (user-facing)
    Route::get('/training', [TrainingController::class, 'index'])->name('training.index');
    Route::get('/training/my-courses', [TrainingController::class, 'myCourses'])->name('training.my-courses');
    Route::get('/training/certificate/{enrollment}', [TrainingController::class, 'certificate'])->name('training.certificate');
    Route::get('/training/{course}', [TrainingController::class, 'show'])->name('training.show');
    Route::post('/training/{course}/enroll', [TrainingController::class, 'enroll'])->name('training.enroll');
    Route::get('/training/{course}/learn', [TrainingController::class, 'learn'])->name('training.learn');
    Route::post('/training/material/{material}/complete', [TrainingController::class, 'markMaterialComplete'])->name('training.material.complete');

    // SLIP GAJI (user-facing)
    Route::get('/my-payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/my-payslips/{detail}', [PayslipController::class, 'show'])->name('payslips.show');
    Route::get('/my-payslips/{detail}/download', [PayslipController::class, 'download'])->name('payslips.download');

    // TASK SAYA (user-facing)
    Route::get('/my-tasks', [MyTaskController::class, 'index'])->name('my-tasks.index');
    Route::get('/my-tasks/{task}', [MyTaskController::class, 'show'])->name('my-tasks.show');
    Route::put('/my-tasks/{task}/status', [MyTaskController::class, 'updateStatus'])->name('my-tasks.update-status');
    Route::post('/my-tasks/{task}/comment', [MyTaskController::class, 'addComment'])->name('my-tasks.comment');
    Route::post('/my-tasks/{task}/start-timer', [MyTaskController::class, 'startTimer'])->name('my-tasks.start-timer');
    Route::post('/my-tasks/{task}/stop-timer', [MyTaskController::class, 'stopTimer'])->name('my-tasks.stop-timer');

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
            Route::patch('/admin/izin/{izin}/approve', [AdminAbsenceController::class, 'approve'])
                ->name('admin.izin.approve');
            Route::patch('/admin/izin/{izin}/reject', [AdminAbsenceController::class, 'reject'])
                ->name('admin.izin.reject');
            Route::patch('/admin/absence-management/{izin}/status', [AdminAbsenceController::class, 'updateStatus'])
                ->name('admin.absence.updateStatus');
        });

        // EMPLOYEE MANAGEMENT - requires manage_employees permission
        Route::middleware(['permission:manage_employees'])->group(function () {
            Route::resource('employees', EmployeeController::class);
            Route::post('/employees/{employee}/approve', [EmployeeController::class, 'approve'])->name('employees.approve');
            Route::post('/employees/{employee}/reject', [EmployeeController::class, 'reject'])->name('employees.reject');
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
            Route::patch('/admin/settings/violation-settings', [AdminSystemSettingController::class, 'updateViolationSettings'])
                ->name('admin.settings.violation-settings.update');
            Route::patch('/admin/settings/anti-cheat', [AdminSystemSettingController::class, 'updateAntiCheat'])
                ->name('admin.settings.anti-cheat.update');
            Route::post('/admin/auto-checkout/trigger', [AdminSystemSettingController::class, 'triggerAutoCheckout'])
                ->name('admin.auto-checkout.trigger');
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
            Route::get('/admin/export/attendance-pdf', [AdminExportController::class, 'exportAttendancePdf'])
                ->name('admin.export.attendance-pdf');
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

        // ANOMALY GPS - requires view_anomaly_attendance
        Route::middleware(['permission:view_anomaly_attendance'])->group(function () {
            Route::get('/admin/anomaly', [AdminAnomalyController::class, 'index'])
                ->name('admin.anomaly.index');
            Route::patch('/admin/anomaly/device/{device}/untrust', [AdminAnomalyController::class, 'markDeviceUntrusted'])
                ->name('admin.anomaly.device.untrust');
        });

        // PAYROLL - requires manage_payroll
        Route::middleware(['permission:manage_payroll'])->group(function () {
            Route::get('/admin/payroll', [AdminPayrollController::class, 'index'])->name('admin.payroll.index');
            Route::get('/admin/payroll/create', [AdminPayrollController::class, 'create'])->name('admin.payroll.create');
            Route::post('/admin/payroll', [AdminPayrollController::class, 'store'])->name('admin.payroll.store');
            Route::get('/admin/payroll/{period}', [AdminPayrollController::class, 'show'])->name('admin.payroll.show');
            Route::post('/admin/payroll/{period}/calculate', [AdminPayrollController::class, 'calculate'])->name('admin.payroll.calculate');
            Route::post('/admin/payroll/{period}/approve', [AdminPayrollController::class, 'approve'])->name('admin.payroll.approve');
            Route::post('/admin/payroll/{period}/mark-paid', [AdminPayrollController::class, 'markPaid'])->name('admin.payroll.mark-paid');
            Route::get('/admin/payroll/{period}/slip/{user}', [AdminPayrollController::class, 'slip'])->name('admin.payroll.slip');
            Route::get('/admin/payroll/{period}/slip/{user}/download', [AdminPayrollController::class, 'downloadSlip'])->name('admin.payroll.slip.download');

            Route::get('/admin/salary-components', [AdminSalaryComponentController::class, 'index'])->name('admin.salary-components.index');
            Route::get('/admin/salary-components/create', [AdminSalaryComponentController::class, 'create'])->name('admin.salary-components.create');
            Route::post('/admin/salary-components', [AdminSalaryComponentController::class, 'store'])->name('admin.salary-components.store');
            Route::get('/admin/salary-components/{salaryComponent}/edit', [AdminSalaryComponentController::class, 'edit'])->name('admin.salary-components.edit');
            Route::put('/admin/salary-components/{salaryComponent}', [AdminSalaryComponentController::class, 'update'])->name('admin.salary-components.update');
            Route::delete('/admin/salary-components/{salaryComponent}', [AdminSalaryComponentController::class, 'destroy'])->name('admin.salary-components.destroy');
            Route::post('/admin/salary-components/reorder', [AdminSalaryComponentController::class, 'reorder'])->name('admin.salary-components.reorder');
            Route::post('/admin/salary-components/validate-formula', [AdminSalaryComponentController::class, 'validateFormula'])->name('admin.salary-components.validate-formula');
            Route::post('/admin/salary-components/preview-formula', [AdminSalaryComponentController::class, 'previewFormula'])->name('admin.salary-components.preview-formula');
            Route::get('/admin/salary-components/variables', [AdminSalaryComponentController::class, 'variables'])->name('admin.salary-components.variables');

            Route::get('/admin/payroll-templates', [AdminPayrollTemplateController::class, 'index'])->name('admin.payroll-templates.index');
            Route::get('/admin/payroll-templates/{template}/preview', [AdminPayrollTemplateController::class, 'preview'])->name('admin.payroll-templates.preview');
            Route::post('/admin/payroll-templates/{template}/apply', [AdminPayrollTemplateController::class, 'apply'])->name('admin.payroll-templates.apply');
            Route::post('/admin/payroll-templates/applications/{application}/rollback', [AdminPayrollTemplateController::class, 'rollback'])->name('admin.payroll-templates.rollback');

            Route::get('/admin/rate-management', [AdminRateManagementController::class, 'index'])->name('admin.rate-management.index');
            Route::get('/admin/rate-management/bpjs', [AdminRateManagementController::class, 'bpjsRates'])->name('admin.rate-management.bpjs');
            Route::get('/admin/rate-management/bpjs/create', [AdminRateManagementController::class, 'createBpjsRate'])->name('admin.rate-management.bpjs.create');
            Route::post('/admin/rate-management/bpjs', [AdminRateManagementController::class, 'storeBpjsRate'])->name('admin.rate-management.bpjs.store');
            Route::get('/admin/rate-management/ter', [AdminRateManagementController::class, 'terRates'])->name('admin.rate-management.ter');
            Route::get('/admin/rate-management/ter/create', [AdminRateManagementController::class, 'createTerRegulation'])->name('admin.rate-management.ter.create');
            Route::post('/admin/rate-management/ter', [AdminRateManagementController::class, 'storeTerRegulation'])->name('admin.rate-management.ter.store');
            Route::get('/admin/rate-management/ptkp', [AdminRateManagementController::class, 'ptkpRates'])->name('admin.rate-management.ptkp');
            Route::get('/admin/rate-management/ptkp/create', [AdminRateManagementController::class, 'createPtkpRates'])->name('admin.rate-management.ptkp.create');
            Route::post('/admin/rate-management/ptkp', [AdminRateManagementController::class, 'storePtkpRates'])->name('admin.rate-management.ptkp.store');
        });

        // VISIT ATTENDANCE - requires view_visit_attendance
        Route::middleware(['permission:view_visit_attendance'])->group(function () {
            Route::get('/admin/visits', [AdminVisitAttendanceController::class, 'index'])->name('admin.visits.index');
            Route::get('/admin/visits/{visitAttendance}', [AdminVisitAttendanceController::class, 'show'])->name('admin.visits.show');
        });

        // LEAVE BALANCE MANAGEMENT - requires manage_leave_balances
        Route::middleware(['permission:manage_leave_balances'])->group(function () {
            Route::get('/admin/leave-balances', [AdminLeaveBalanceController::class, 'index'])
                ->name('admin.leave-balances.index');
            Route::patch('/admin/leave-balances/{leaveBalance}', [AdminLeaveBalanceController::class, 'update'])
                ->name('admin.leave-balances.update');
            Route::post('/admin/leave-balances/initialize', [AdminLeaveBalanceController::class, 'initialize'])
                ->name('admin.leave-balances.initialize');
        });

        // RECRUITMENT - requires manage_recruitment
        Route::middleware(['permission:manage_recruitment'])->group(function () {
            Route::get('/admin/recruitment', [AdminRecruitmentController::class, 'index'])->name('admin.recruitment.index');
            Route::get('/admin/recruitment/positions', [AdminRecruitmentController::class, 'positions'])->name('admin.recruitment.positions');
            Route::get('/admin/recruitment/positions/create', [AdminRecruitmentController::class, 'createPosition'])->name('admin.recruitment.positions.create');
            Route::post('/admin/recruitment/positions', [AdminRecruitmentController::class, 'storePosition'])->name('admin.recruitment.positions.store');
            Route::get('/admin/recruitment/positions/{jobPosition}', [AdminRecruitmentController::class, 'showPosition'])->name('admin.recruitment.positions.show');
            Route::get('/admin/recruitment/positions/{jobPosition}/edit', [AdminRecruitmentController::class, 'editPosition'])->name('admin.recruitment.positions.edit');
            Route::put('/admin/recruitment/positions/{jobPosition}', [AdminRecruitmentController::class, 'updatePosition'])->name('admin.recruitment.positions.update');
            Route::post('/admin/recruitment/positions/{jobPosition}/candidates', [AdminRecruitmentController::class, 'addCandidate'])->name('admin.recruitment.candidates.store');
            Route::get('/admin/recruitment/candidates/{candidate}', [AdminRecruitmentController::class, 'showCandidate'])->name('admin.recruitment.candidates.show');
            Route::patch('/admin/recruitment/candidates/{candidate}/status', [AdminRecruitmentController::class, 'updateCandidateStatus'])->name('admin.recruitment.candidates.update-status');
            Route::post('/admin/recruitment/candidates/{candidate}/interviews', [AdminRecruitmentController::class, 'scheduleInterview'])->name('admin.recruitment.interviews.store');
            Route::put('/admin/recruitment/interviews/{interview}', [AdminRecruitmentController::class, 'updateInterview'])->name('admin.recruitment.interviews.update');
            Route::post('/admin/recruitment/candidates/{candidate}/onboarding', [AdminRecruitmentController::class, 'initOnboarding'])->name('admin.recruitment.onboarding.init');
            Route::patch('/admin/recruitment/onboarding/{candidateOnboarding}', [AdminRecruitmentController::class, 'updateOnboardingTask'])->name('admin.recruitment.onboarding.update');
        });

        // VIOLATIONS - requires manage_violations
        Route::middleware(['permission:manage_violations'])->group(function () {
            Route::get('/admin/violations', [AdminViolationController::class, 'index'])->name('admin.violations.index');
            Route::get('/admin/violations/create', [AdminViolationController::class, 'create'])->name('admin.violations.create');
            Route::post('/admin/violations', [AdminViolationController::class, 'store'])->name('admin.violations.store');
            Route::get('/admin/violations/monthly-report', [AdminViolationController::class, 'monthlyReport'])->name('admin.violations.monthly-report');
        });

        // ASSET MANAGEMENT - requires manage_assets
        Route::middleware(['permission:manage_assets'])->group(function () {
            Route::get('/admin/assets', [AdminAssetController::class, 'index'])->name('admin.assets.index');
            Route::get('/admin/assets/create', [AdminAssetController::class, 'create'])->name('admin.assets.create');
            Route::post('/admin/assets', [AdminAssetController::class, 'store'])->name('admin.assets.store');
            Route::get('/admin/assets/categories', [AdminAssetController::class, 'categories'])->name('admin.assets.categories');
            Route::post('/admin/assets/categories', [AdminAssetController::class, 'storeCategory'])->name('admin.assets.categories.store');
            Route::put('/admin/assets/categories/{category}', [AdminAssetController::class, 'updateCategory'])->name('admin.assets.categories.update');
            Route::delete('/admin/assets/categories/{category}', [AdminAssetController::class, 'destroyCategory'])->name('admin.assets.categories.destroy');
            Route::get('/admin/assets/{asset}', [AdminAssetController::class, 'show'])->name('admin.assets.show');
            Route::get('/admin/assets/{asset}/edit', [AdminAssetController::class, 'edit'])->name('admin.assets.edit');
            Route::put('/admin/assets/{asset}', [AdminAssetController::class, 'update'])->name('admin.assets.update');
            Route::delete('/admin/assets/{asset}', [AdminAssetController::class, 'destroy'])->name('admin.assets.destroy');
            Route::get('/admin/assets/{asset}/assign', [AdminAssetController::class, 'assign'])->name('admin.assets.assign');
            Route::post('/admin/assets/{asset}/assign', [AdminAssetController::class, 'storeAssignment'])->name('admin.assets.assign.store');
            Route::post('/admin/assets/{asset}/return', [AdminAssetController::class, 'returnAsset'])->name('admin.assets.return');
            Route::post('/admin/assets/{asset}/maintenance', [AdminAssetController::class, 'storeMaintenance'])->name('admin.assets.maintenance.store');
        });

        // TICKETS - requires manage_tickets
        Route::middleware(['permission:manage_tickets'])->group(function () {
            Route::get('/admin/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
            Route::get('/admin/tickets/dashboard', [AdminTicketController::class, 'dashboard'])->name('admin.tickets.dashboard');
            Route::get('/admin/tickets/categories', [AdminTicketController::class, 'categories'])->name('admin.tickets.categories');
            Route::post('/admin/tickets/categories', [AdminTicketController::class, 'storeCategory']);
            Route::put('/admin/tickets/categories/{category}', [AdminTicketController::class, 'updateCategory']);
            Route::delete('/admin/tickets/categories/{category}', [AdminTicketController::class, 'destroyCategory']);
            Route::get('/admin/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
            Route::post('/admin/tickets/{ticket}/assign', [AdminTicketController::class, 'assign']);
            Route::put('/admin/tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus']);
            Route::post('/admin/tickets/{ticket}/comment', [AdminTicketController::class, 'addComment']);
        });

        // FORM INTERNAL - requires manage_forms
        Route::middleware(['permission:manage_forms'])->group(function () {
            Route::get('/admin/forms', [AdminFormController::class, 'index'])->name('admin.forms.index');
            Route::get('/admin/forms/create', [AdminFormController::class, 'create'])->name('admin.forms.create');
            Route::post('/admin/forms', [AdminFormController::class, 'store'])->name('admin.forms.store');
            Route::get('/admin/forms/{template}/edit', [AdminFormController::class, 'edit'])->name('admin.forms.edit');
            Route::put('/admin/forms/{template}', [AdminFormController::class, 'update'])->name('admin.forms.update');
            Route::delete('/admin/forms/{template}', [AdminFormController::class, 'destroy'])->name('admin.forms.destroy');
            Route::get('/admin/forms/{template}/submissions', [AdminFormController::class, 'submissions'])->name('admin.forms.submissions');
            Route::get('/admin/forms/submissions/{submission}', [AdminFormController::class, 'showSubmission'])->name('admin.forms.submission.show');
            Route::post('/admin/forms/submissions/{submission}/approve', [AdminFormController::class, 'approve'])->name('admin.forms.submission.approve');
            Route::post('/admin/forms/submissions/{submission}/reject', [AdminFormController::class, 'reject'])->name('admin.forms.submission.reject');
            Route::post('/admin/forms/submissions/{submission}/comment', [AdminFormController::class, 'addComment'])->name('admin.forms.submission.comment');
            Route::get('/admin/forms/{template}/export', [AdminFormController::class, 'export'])->name('admin.forms.export');
        });

        Route::middleware(['permission:manage_projects'])->group(function () {
            Route::resource('admin/projects', AdminProjectController::class)->names('admin.projects');
            Route::post('admin/tasks', [AdminTaskController::class, 'store'])->name('admin.tasks.store');
            Route::put('admin/tasks/{task}', [AdminTaskController::class, 'update'])->name('admin.tasks.update');
            Route::patch('admin/tasks/{task}/status', [AdminTaskController::class, 'updateStatus'])->name('admin.tasks.update-status');
            Route::delete('admin/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('admin.tasks.destroy');
            Route::post('admin/tasks/{task}/comments', [AdminTaskController::class, 'addComment'])->name('admin.tasks.add-comment');
            Route::post('admin/tasks/{task}/timer/start', [AdminTaskController::class, 'startTimer'])->name('admin.tasks.timer.start');
            Route::patch('admin/time-entries/{timeEntry}/stop', [AdminTaskController::class, 'stopTimer'])->name('admin.tasks.timer.stop');
        });

        // TRAINING - requires manage_training
        Route::middleware(['permission:manage_training'])->group(function () {
            Route::get('/admin/training', [AdminTrainingController::class, 'index'])->name('admin.training.index');
            Route::get('/admin/training/create', [AdminTrainingController::class, 'create'])->name('admin.training.create');
            Route::post('/admin/training', [AdminTrainingController::class, 'store'])->name('admin.training.store');
            Route::get('/admin/training/report', [AdminTrainingController::class, 'report'])->name('admin.training.report');
            Route::get('/admin/training/{course}', [AdminTrainingController::class, 'show'])->name('admin.training.show');
            Route::get('/admin/training/{course}/edit', [AdminTrainingController::class, 'edit'])->name('admin.training.edit');
            Route::put('/admin/training/{course}', [AdminTrainingController::class, 'update'])->name('admin.training.update');
            Route::delete('/admin/training/{course}', [AdminTrainingController::class, 'destroy'])->name('admin.training.destroy');
            Route::post('/admin/training/{course}/publish', [AdminTrainingController::class, 'publish']);
            Route::post('/admin/training/{course}/unpublish', [AdminTrainingController::class, 'unpublish']);
            Route::post('/admin/training/{course}/materials', [AdminTrainingController::class, 'storeMaterial']);
            Route::put('/admin/training/materials/{material}', [AdminTrainingController::class, 'updateMaterial']);
            Route::delete('/admin/training/materials/{material}', [AdminTrainingController::class, 'destroyMaterial']);
            Route::post('/admin/training/{course}/enroll', [AdminTrainingController::class, 'enrollEmployee']);
        });

        // COMPANY MANAGEMENT - requires manage_companies
        Route::middleware(['permission:manage_companies'])->group(function () {
            Route::get('/admin/companies', [AdminCompanyController::class, 'index'])->name('admin.companies.index');
            Route::get('/admin/companies/create', [AdminCompanyController::class, 'create'])->name('admin.companies.create');
            Route::post('/admin/companies', [AdminCompanyController::class, 'store'])->name('admin.companies.store');
            Route::get('/admin/companies/{company}', [AdminCompanyController::class, 'show'])->name('admin.companies.show');
            Route::get('/admin/companies/{company}/edit', [AdminCompanyController::class, 'edit'])->name('admin.companies.edit');
            Route::put('/admin/companies/{company}', [AdminCompanyController::class, 'update'])->name('admin.companies.update');
            Route::delete('/admin/companies/{company}', [AdminCompanyController::class, 'destroy'])->name('admin.companies.destroy');
            Route::post('/admin/companies/{company}/branches', [AdminCompanyController::class, 'storeBranch']);
            Route::put('/admin/companies/branches/{branch}', [AdminCompanyController::class, 'updateBranch']);
            Route::delete('/admin/companies/branches/{branch}', [AdminCompanyController::class, 'destroyBranch']);
            Route::get('/admin/companies/{company}/settings', [AdminCompanyController::class, 'settings'])->name('admin.companies.settings');
            Route::put('/admin/companies/{company}/settings', [AdminCompanyController::class, 'updateSettings']);
            Route::post('/admin/companies/{company}/assign-employee', [AdminCompanyController::class, 'assignEmployee']);
            Route::post('/admin/companies/transfer-employee', [AdminCompanyController::class, 'transferEmployee']);
        });
    });
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
