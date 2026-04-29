# Enterprise Upgrade Plan — Change Summary (Oracle Review Integration)

**Date**: 2026-04-29  
**Status**: ✅ ALL ORACLE FINDINGS INTEGRATED  
**Full Review**: See `ORACLE-REVIEW-FINDINGS.md`

---

## What Changed

### 🚨 3 CRITICAL FIXES ADDED

1. **Task 1B.6 (NEW)**: Data Migration Strategy
   - `CheckPayrollReadiness` artisan command
   - `isPayrollReady()` method on User model
   - Bulk-edit UI for populating employee data
   - CSV import for mass data population
   - PayrollService skips employees with incomplete data

2. **Task 2.4 Step A.5 (NEW)**: Mid-Month Proration
   - `prorate_factor` calculation for join/leave mid-month
   - Applied to gaji_pokok, fixed components, BPJS base
   - `prorate_factor` and `prorate_reason` columns added to payroll_details

3. **Task 2.12 (NEW)**: THR Calculation
   - `ThrCalculator` service (tenure-based calculation)
   - Integration with PayrollService
   - Admin UI with date picker
   - System settings: `thr_includes_allowances`, `thr_min_tenure_months`

### ⚠️ 12 CONCERNS ADDRESSED

| # | Concern | Fix Location |
|---|---|---|
| C1 | BPJS JP ceiling time-sensitive | Task 2.5 — admin reminder note added |
| C2 | No-NPWP surcharge ambiguous | Task 2.6 — made configurable toggle |
| C3 | Phase 3B can decouple from Phase 2 | Dependency graph updated |
| C4 | GPS variance threshold too aggressive | Task 1D.4 — byte-identical check |
| C5 | Speed anomaly threshold too high | Task 1D.4 — contextual thresholds |
| C6 | LBP liveness weak | Task 1D.6 — MiniFASNet primary (60%) |
| C7 | leave_balances.remaining storage | Task 3B.1/3B.2 — reconciliation added |
| C8 | Observer audit log performance | Task 1A.3 — `withoutAuditLog()` bypass |
| C9 | PayrollFreezeMiddleware fragile | Task 2.10 — model-level trait |
| C10 | Payroll calculation should be queued | Task 2.4 — Laravel Queue + Bus::batch() |
| C11 | employee_salaries redundant | Task 2.1/2.2 — renamed to employee_salary_components |
| C12 | late_minutes column missing | Task 1C.1 — added to attendances table |

### 📌 5 MISSING ITEMS ADDED

| # | Item | Fix Location |
|---|---|---|
| M1 | Payroll approval workflow undefined | Task 2.7 — separate `approve_payroll` permission |
| M2 | Rate limiting on attendance API | Task 0.1 — `throttle:6,1` added |
| M3 | Face-service downtime handling | Task 0.3 (NEW) — health check + fallback |
| M4 | Payroll recalculation flow | Task 2.4 method (4) — recalculate() |
| M5 | BPJS Kes base calculation | Task 2.5 — total fixed earnings, not just gaji_pokok |

---

## Task Count Changes

| Phase | Before | After | Change |
|---|---|---|---|
| Phase 0 | 2 tasks | 3 tasks | +1 (health check) |
| Phase 1B | 5 tasks | 6 tasks | +1 (migration gate) |
| Phase 2 | 11 tasks | 12 tasks | +1 (THR) |
| **Total** | **50 tasks** | **53 tasks** | **+3 tasks** |

---

## Execution Timeline Changes

| Metric | Before | After | Change |
|---|---|---|---|
| Total Duration | ~7 weeks | ~8 weeks | +1 week (THR + migration) |
| Critical Path | Phase 0→1A→1B→1C→2→3B | Phase 0→1A→1B→1C→2→3B | Same |
| Parallelization | 1C ‖ 1D | 1C ‖ 1D, 1B ‖ 3B.1-3 | +1 parallel track |
| **Net Time Saved** | — | **~1 week** | Via 3B decoupling |

**Revised Total**: ~8 weeks (was 7, +1 for new tasks, -1 via parallelization = net +0, but more robust)

---

## Key Architectural Changes

### 1. Payroll Calculation Flow (Task 2.4)
```
BEFORE:
calculateAll() → loop employees → save

AFTER:
calculateAll() → dispatch CalculatePayrollJob
  → Bus::batch(chunks of 10)
  → skip if !isPayrollReady()
  → apply prorate_factor
  → withoutAuditLog() for bulk items
  → update status to 'calculated'
```

### 2. Anti-Cheat GPS (Task 1D.4)
```
BEFORE:
- GPS variance < 0.000001 → flag
- Speed > 200 km/h → flag

AFTER:
- All 3 readings byte-identical → flag
- Contextual speed: <2min=5km/h, <15min=30km/h, <1h=120km/h, any=200km/h
```

### 3. Liveness Detection (Task 1D.6)
```
BEFORE:
- LBP texture analysis only

AFTER:
- MiniFASNet (60% weight) — primary
- LBP texture (25% weight) — supplementary
- Color distribution (15% weight) — supplementary
- Combined liveness_score (0.0-1.0)
```

### 4. Payroll Freeze (Task 2.10)
```
BEFORE:
- Middleware only (HTTP requests)

AFTER:
- PayrollFreezeTrait (model-level, all entry points)
- PayrollFreezeMiddleware (UX warning banner)
- PayrollFrozenException (global handler)
```

---

## Database Schema Changes

### New Columns Added

**attendances** (Task 1C.1):
- `late_minutes` (integer nullable)

**payroll_details** (Task 2.1):
- `prorate_factor` (decimal 5,4 default 1.0000)
- `prorate_reason` (string nullable)

**payroll_periods** (Task 2.1):
- `reviewed_by` (foreignId nullable)
- `reviewed_at` (timestamp nullable)

**leave_balances** (Task 3B.1):
- CHECK constraint: `remaining >= 0`

### Table Renamed

**employee_salaries** → **employee_salary_components** (Task 2.1)
- Removed `gaji_pokok` column (now only on users table)
- Stores ONLY additional components (tunjangan jabatan, transport, etc.)

---

## System Settings Added

**Phase 0** (Task 0.1):
- (none — uses existing throttle config)

**Phase 1D** (Task 1D.2):
- `max_devices_per_user` (integer, default 2)
- `anomaly_score_warning_threshold` (integer, default 30)
- `anomaly_score_reject_threshold` (integer, default 60)
- `enable_anti_cheat` (boolean, default true)

**Phase 2** (Task 2.5, 2.6, 2.12):
- `bpjs_kes_ceiling` (decimal, default 12000000)
- `bpjs_jp_ceiling` (decimal, default 10042300) — **NOTE: changes annually every March**
- `jkk_risk_group` (integer, default 1)
- `pph21_no_npwp_surcharge_enabled` (boolean, default true)
- `pph21_no_npwp_surcharge_rate` (decimal, default 0.20)
- `thr_includes_allowances` (boolean, default true)
- `thr_min_tenure_months` (integer, default 1)

**Phase 3B** (Task 3B.2):
- `max_carry_over_days` (integer, default 5)

---

## Permissions Added/Modified

**New Permissions**:
- `approve_payroll` (Direktur only) — separate from `manage_payroll`
- `view_visit_attendance` (Manager+)

**Modified Permissions**:
- `manage_payroll` (Direktur, VP) — create/calculate/recalculate only
- `view_payroll` (Manager) — read-only

---

## Commands Added

**Phase 1B** (Task 1B.6):
- `php artisan payroll:check-readiness` — reports employees missing payroll fields

**Phase 1C** (Task 1C.5):
- `php artisan violations:generate-alpha` — daily alpha violations

**Phase 1A** (Task 1A.5):
- `php artisan audit:clean-old` — daily cleanup

**Phase 3B** (Task 3B.2):
- `php artisan leave:reconcile-balances` — nightly reconciliation

**Phase 3D** (Task 3D.2):
- `php artisan attendance:auto-checkout` — daily auto-checkout

---

## Services Added/Modified

**New Services**:
- `ThrCalculator` (Task 2.12)
- `AntiCheatService` (Task 1D.4)
- `SlipGajiPdfService` (Task 2.9)

**Modified Services**:
- `PayrollService` — added proration, THR, queue, recalculate
- `BpjsCalculator` — base changed to total fixed earnings
- `Pph21Calculator` — added configurable no-NPWP surcharge
- `ViolationService` — stores late_minutes on attendance
- `LeaveService` — added reconciliation, transaction safety

---

## Test Cases Added

**Phase 2** (Task 2.11):
- Mid-month join proration (employee joins 15th, prorate_factor ≈ 0.5)
- Mid-month leave proration
- Employee with `isPayrollReady() = false` is skipped
- THR calculation (tenure >= 12 months, tenure >= 1 month, tenure < 1 month)
- BPJS base uses total fixed earnings, not just gaji_pokok
- Employee without NPWP (configurable surcharge)

---

## Breaking Changes

### 1. employee_salaries Table Renamed
**Impact**: Any existing code referencing `EmployeeSalary` model must be updated to `EmployeeSalaryComponent`.

**Migration Path**: None — this is a new table in Phase 2. No existing data.

### 2. gaji_pokok Source of Truth
**Impact**: `gaji_pokok` is now ONLY on `users` table. Do not store in `employee_salary_components`.

**Migration Path**: Seeder and documentation updated to reflect this.

### 3. Payroll Freeze Behavior
**Impact**: Payroll freeze now blocks ALL modifications (HTTP, artisan, queue, direct model), not just HTTP requests.

**Migration Path**: Existing code that modifies attendances/violations/izins during payroll processing will now throw `PayrollFrozenException`.

---

## Verification Checklist

Before starting Phase 0:
- [ ] Read `ORACLE-REVIEW-FINDINGS.md` in full
- [ ] Understand proration logic (Task 2.4 Step A.5)
- [ ] Understand THR calculation rules (Task 2.12)
- [ ] Understand data migration strategy (Task 1B.6)
- [ ] Review contextual speed anomaly thresholds (Task 1D.4)
- [ ] Review MiniFASNet liveness detection (Task 1D.6)

Before starting Phase 2:
- [ ] Verify all employees have `isPayrollReady() = true` via `php artisan payroll:check-readiness`
- [ ] Populate missing employee data via bulk-edit UI or CSV import
- [ ] Test proration calculation with mid-month join/leave scenarios
- [ ] Test THR calculation with various tenure scenarios

---

## Oracle Review Status

| Category | Status |
|---|---|
| 3 Critical Issues | ✅ ALL FIXED |
| 12 Concerns | ✅ ALL ADDRESSED |
| 5 Missing Items | ✅ ALL ADDED |
| Dependency Graph | ✅ OPTIMIZED |
| Execution Order | ✅ REVISED |

**Plan Readiness**: ✅ **READY FOR EXECUTION**

---

## Next Steps

1. **User Approval**: Present this change summary to user for final approval
2. **Plan Finalization**: Once approved, the full updated plan is in `enterprise-upgrade.md`
3. **Execution Start**: Begin with Phase 0 Task 0.1 (Auth API fix)

**Estimated Total Effort**: ~8 weeks (53 tasks across 10 phases)
