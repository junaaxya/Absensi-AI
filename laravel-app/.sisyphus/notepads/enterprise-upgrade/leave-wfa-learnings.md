# Phase 3B: Leave & WFA Workflow - Learnings

## Architecture Decisions

### Multi-Level Approval Chain
- Role hierarchy mapped as constants in LeaveService: Staf/Magang -> TL -> Manager -> VP -> Direktur
- Approval status uses enum: pending -> approved_l1 -> approved_l2 -> approved_final
- Short chains (e.g., VP -> Direktur) skip L2 and go straight to approved_final
- `current_approver_id` tracks who needs to act next; nulled on final approval or rejection

### Existing Controller Pattern
- Admin izin approval was split between IzinController (approve/reject) and AdminAbsenceController (index/updateStatus)
- Consolidated approval logic into AdminAbsenceController to keep it consistent
- Routes updated to point to AdminAbsenceController for approve/reject
- IzinController kept for user-facing CRUD only

### Leave Balance Design
- Unique constraint on [user_id, leave_type_id, year] prevents duplicate balances
- `getBalance()` uses firstOrCreate for lazy initialization
- Balance deduction only happens on `approved_final` status, not intermediate approvals
- Rejection restores balance if it was partially deducted (edge case safety)

### WFA (Work From Anywhere)
- Added as new jenis option alongside izin/sakit/cuti/dinas
- wfa_location is required when jenis=wfa
- wfa_daily_checkins stores GPS check-in array as JSON
- Check-in endpoint validates user ownership and jenis=wfa

## Key Files Modified/Created

### New Files
- `database/migrations/2026_05_03_000002_enhance_leave_system.php`
- `app/Models/LeaveBalance.php`
- `app/Services/LeaveService.php`
- `app/Http/Controllers/AdminLeaveBalanceController.php`
- `resources/views/admin/leave-balances/index.blade.php`

### Modified Files
- `app/Models/Izin.php` - Added new fillable fields, casts, relationships
- `app/Models/User.php` - Added leaveBalances() relationship
- `app/Http/Controllers/IzinController.php` - Enhanced store(), added getLeaveBalance(), wfaCheckin()
- `app/Http/Controllers/AdminAbsenceController.php` - Added approve/reject with LeaveService
- `app/Http/Controllers/AttendanceController.php` - Pass leaveTypes to dashboard
- `routes/web.php` - Added leave balance routes, WFA check-in route, updated approval routes
- `database/seeders/RoleAndPermissionSeeder.php` - Added manage_leave_balances permission
- `resources/views/dashboard.blade.php` - Enhanced izin modal with WFA and leave type selection
- `resources/views/izin/index.blade.php` - Added balance cards, approval chain indicator
- `resources/views/admin/absence.blade.php` - Added approval level column, reject modal with reason
- `resources/views/admin/izin/index.blade.php` - Added approval badges, conditional approve buttons
- `resources/views/layouts/admin.blade.php` - Added "Saldo Cuti" sidebar item

## Gotchas
- PayrollFreezeTrait must NOT be removed from Izin model
- Admin views use `layouts.admin`, user views use `layouts.absensi`
- The admin absence view (`admin.absence`) is the primary admin view, `admin.izin.index` is secondary
- Approval buttons only show when current_approver_id matches auth user OR user has Direktur/VP role
- LeaveType::active() scope already exists and is reused
