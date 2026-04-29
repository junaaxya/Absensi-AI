# Oracle Review Findings — Enterprise Upgrade Plan

**Review Date**: 2026-04-29  
**Reviewer**: Oracle (claude-opus-4.6)  
**Plan Version**: enterprise-upgrade.md (initial)

---

## Executive Summary

**Verdict**: Plan is well-structured and demonstrates strong domain knowledge of Indonesian tax/labor law. **3 CRITICAL ISSUES** must be addressed before execution. 12 concerns require adjustments. 5 missing items should be added.

**Overall Readiness**: 75% — Core architecture is sound, but critical gaps in data migration, proration, and THR compliance.

---

## ✅ APPROVED (No Changes Needed)

1. **Phase 0 Security Fixes** — Auth API vulnerability correctly identified
2. **Phase 1A Audit Log** — Observer pattern + HasAuditLog trait is standard
3. **PPh 21 TER Logic** — Follows PP 58/2023 accurately
4. **PTKP Values** — All 8 values verified for 2024
5. **TER Category Mapping** — A/B/C categories correct
6. **BPJS Rates** — JHT 5.7%, JKK 0.24-1.74%, JKM 0.3%, JP 3%, BPJS Kes 5% all correct
7. **Violation Points System** — Auto-generation + SP1/SP2/SP3 thresholds are standard HR practice
8. **Visit Attendance** — GPS trail design is solid
9. **Overtime Formula** — gaji/173, 1.5×, 2.0× per UU Cipta Kerja correct
10. **Database Denormalization** — payroll_details + payroll_detail_items is intentional and correct

---

## 🚨 CRITICAL ISSUES (Must Fix Before Execution)

### CRITICAL 1: Missing Data Migration Strategy for Existing Employees

**Impact**: Phase 1B adds 19 new fields to `users` table. ALL existing employees will have NULL values. Phase 2 Payroll will crash or produce Rp 0 salaries.

**Problems**:
- `PayrollService.calculateForEmployee()` needs `gaji_pokok > 0` — NULL/0 produces zero payroll
- `Pph21Calculator` needs `status_pernikahan` + `jumlah_tanggungan` for TER category — NULL has no mapping
- `BpjsCalculator` needs `gaji_pokok` as base — NULL/0 produces zero BPJS
- No validation gate preventing payroll calculation for incomplete profiles

**Required Fix**:
```
Add Task 1B.6: Data Migration & Validation Gate

1. Create artisan command `CheckPayrollReadiness` that reports which 
   employees are missing required payroll fields
   
2. Add `isPayrollReady(): bool` accessor to User model:
   - gaji_pokok > 0
   - status_pernikahan is not null
   - jumlah_tanggungan is not null
   - tanggal_masuk is not null
   
3. In PayrollService::calculateAll(), skip employees where 
   isPayrollReady() === false and log as "skipped - incomplete data"
   
4. Show "incomplete profile" warnings on admin employee list

5. Create bulk-edit UI or CSV import for populating existing employee data
```

**Status**: ✅ ADDED to plan as Task 1B.6

---

### CRITICAL 2: Mid-Month Join/Leave Proration Missing

**Impact**: No logic for employees who join/leave mid-month. First payroll run will overpay or underpay.

**Required Fix**:
```
Add to Task 2.4 (PayrollService):

Step A.5: Proration Logic
- If user.tanggal_masuk falls within payroll period:
  prorate_factor = working_days_after_join / total_working_days
  
- If user.tanggal_keluar falls within payroll period:
  prorate_factor = working_days_before_leave / total_working_days
  
- Apply prorate_factor to: gaji_pokok, all fixed components, BPJS base

- Variable components (overtime, attendance bonus) are naturally 
  prorated by actual attendance
  
- Store prorate_factor and prorate_reason in payroll_details

Add columns to payroll_details:
  prorate_factor (decimal 5,4 default 1.0000)
  prorate_reason (string nullable)
```

**Status**: ✅ ADDED to plan in Task 2.4

---

### CRITICAL 3: THR (Tunjangan Hari Raya) Completely Missing

**Impact**: THR is **legally mandatory** in Indonesia (PP 36/2021). Omitting it is a compliance gap.

**Key Rules**:
- Employees with ≥12 months tenure: THR = 1× monthly salary
- Employees with ≥1 month but <12 months: THR = (months_worked / 12) × monthly salary
- Must be paid at least 7 days before Hari Raya
- THR is taxable income (included in PPh 21 for that month)

**Required Fix**:
```
Add Task 2.12: THR Calculation

1. Create ThrCalculator service:
   - calculate(User, thrDate): decimal
   - tenure = tanggal_masuk to thrDate in months
   - if tenure >= 12: thr = gaji_pokok + fixed_allowances
   - if tenure >= 1: thr = (tenure / 12) * (gaji_pokok + fixed_allowances)
   - if tenure < 1: thr = 0 (or company policy)
   
2. THR is added to payroll period containing Hari Raya date

3. For PPh 21 TER: THR is added to bruto for that month

4. Add admin UI to trigger THR calculation with date picker

5. Add system_setting: thr_includes_allowances (boolean, default true)
```

**Status**: ✅ ADDED to plan as Task 2.12

---

## ⚠️ CONCERNS (Should Fix During Execution)

### C1. BPJS JP Ceiling Is Time-Sensitive
**Issue**: Hardcoded Rp 10.042.300 is correct for March 2024 – February 2025 only. Changes to Rp 10.547.400 from March 2025.

**Fix**: Already in system_settings (good). Add admin reminder/notification for annual adjustment every March.

**Status**: ✅ NOTED in plan Task 2.5

---

### C2. No-NPWP 20% Surcharge Is Legally Ambiguous Post-2024
**Issue**: 2024 NIK-NPWP integration makes this unclear. DJP's e-Bupot 21/26 doesn't enforce it.

**Fix**: Make it a **configurable toggle** in system settings (default: enabled) with admin note explaining ambiguity.

**Status**: ✅ ADDED to plan Task 2.6 as configurable

---

### C3. Phase 3B → Phase 2 Dependency Can Be Partially Decoupled
**Issue**: 90% of Phase 3B (leave/WFA) has zero payroll dependency. Only unpaid leave deduction needs Phase 2.

**Fix**: Split execution — start 3B.1-3B.4 after Phase 1A, wire unpaid leave deduction in Phase 2 Task 2.4.

**Status**: ✅ UPDATED dependency graph

---

### C4. GPS Variance Threshold Too Aggressive
**Issue**: `variance < 0.000001` (11cm) will flag legitimate stationary GPS. Modern phones can produce this.

**Fix**: Check if all 3 readings are **byte-identical** (exact same float values) instead. Real GPS NEVER produces identical floats.

**Status**: ✅ UPDATED Task 1D.4

---

### C5. Speed Anomaly Threshold Too High
**Issue**: 200 km/h only catches teleportation. Misses realistic spoofing (20km away, 8 hours ago = 2.5 km/h passes).

**Fix**: Add **contextual speed check**:
- < 2 min: max 5 km/h (walking)
- < 15 min: max 30 km/h (city)
- < 1 hour: max 120 km/h (highway)
- Any gap: max 200 km/h (absolute ceiling)

**Status**: ✅ UPDATED Task 1D.4

---

### C6. LBP Liveness Detection Is Weak
**Issue**: LBP alone is easily defeated by high-res screens, printed photos, video replay.

**Fix**: Upgrade to **MiniFASNet** (Silent-Face-Anti-Spoofing) as primary method. Apache 2.0 licensed, ~4MB, <10ms inference. Industry standard for InsightFace systems. Keep LBP as supplementary signal.

**Status**: ✅ UPDATED Task 1D.6 to use MiniFASNet (60%) + LBP (25%) + Color (15%)

---

### C7. leave_balances.remaining — Store It, But With Safeguards
**Issue**: Should `remaining` be computed or stored?

**Fix**: **Store it** (correct for performance), but add:
- `recalculateBalance()` method that recomputes from source of truth
- Nightly reconciliation scheduler that logs discrepancies
- Database constraint: `CHECK (remaining >= 0)`
- Use DB::transaction() when deducting to prevent race conditions

**Status**: ✅ ADDED to Task 3B.1 and 3B.2

---

### C8. Observer Pattern Performance for Bulk Operations
**Issue**: `HasAuditLog` trait fires on every model event. During payroll bulk calculation (100 employees × 10 items = 1,100 inserts), this is slow.

**Fix**: Add `static::withoutAuditLog(callable $callback)` bypass method. Use for bulk payroll operations where PayrollPeriod itself is the audit record.

**Status**: ✅ ADDED to Task 1A.3

---

### C9. PayrollFreezeMiddleware Approach Is Fragile
**Issue**: Middleware only blocks HTTP requests. Artisan commands, queue jobs, direct model operations bypass it.

**Fix**: Move freeze logic to **model level** via `PayrollFreezeTrait` that checks in `saving`/`deleting` events. Keep middleware as secondary UX layer (warning banner).

**Status**: ✅ UPDATED Task 2.10 to use trait + middleware

---

### C10. Payroll Calculation Should Be Queued
**Issue**: `calculateAll()` loops synchronously. For 100+ employees, this takes 30-60 seconds → HTTP timeout.

**Fix**: Dispatch `CalculatePayrollJob` to queue, process in chunks of 10 using `Bus::batch()`. Update status to 'processing' immediately, then 'calculated' when done. Add polling endpoint for progress.

**Status**: ✅ UPDATED Task 2.4 to use Laravel Queue

---

### C11. employee_salaries Table May Be Redundant
**Issue**: `employee_salaries` has `gaji_pokok` but Phase 1B also adds `gaji_pokok` to `users` table. Data duplication — which is source of truth?

**Fix**: **Option A (Chosen)**: Keep `gaji_pokok` on `users` table as canonical source. Rename `employee_salaries` to `employee_salary_components` and store ONLY additional components (tunjangan jabatan, transport, etc.). Remove `gaji_pokok` from this table.

**Status**: ✅ UPDATED Task 2.1 and 2.2 to use `employee_salary_components`

---

### C12. Missing late_minutes Column in Attendances
**Issue**: Task 1C.4 says "Calculate late minutes from jam_masuk vs shift time" but late minutes are NOT stored. ViolationService would need to recalculate (unreliable if shift changes retroactively).

**Fix**: Add `late_minutes` column (integer nullable) to `attendances` table. Calculate and store during check-in.

**Status**: ✅ ADDED to Task 1C.1 migration

---

## 📌 MISSING ITEMS (Should Be Added)

### M1. Payroll Approval Workflow Not Defined
**Issue**: Task 2.7 mentions `approve` method but doesn't define WHO can approve. Permission is just `manage_payroll` for Direktur and VP.

**Fix**: Define approval chain:
- HR/Finance creates and calculates → status: `calculated`
- Manager reviews → status: `reviewed` (optional)
- Direktur/VP approves → status: `approved`
- Finance marks paid → status: `paid` → auto-lock

Add `approve_payroll` permission separate from `manage_payroll`.

**Status**: ✅ ADDED to Task 2.7 with separate permissions

---

### M2. Rate Limiting on Attendance API
**Issue**: Even after adding `auth:sanctum`, there's no rate limiting. Compromised token could flood the system.

**Fix**: Apply `throttle:6,1` (6 attempts per minute) to attendance API endpoint.

**Status**: ✅ ADDED to Task 0.1

---

### M3. Face-Service Downtime Handling
**Issue**: If face-service is down, attendance returns 500 "Face Service Unavailable." No fallback.

**Fix**: 
- Add `/health` endpoint to face-service
- Show "Face service offline" banner on dashboard
- Allow admin manual attendance override (with audit log)
- Add retry logic with exponential backoff

**Status**: ✅ ADDED as Task 0.3

---

### M4. Payroll Recalculation & Correction
**Issue**: What happens when attendance is corrected AFTER payroll is calculated? No recalculation flow.

**Fix**: Add `recalculate` action on PayrollPeriod that:
- Only works on `calculated` status (not approved/paid)
- Deletes existing PayrollDetails and recalculates
- Logs recalculation in audit log with reason

**Status**: ✅ ADDED to Task 2.4 as method (4)

---

### M5. BPJS Kesehatan Base Calculation
**Issue**: Plan says `base = min(gajiPokok, ceiling)` but actual base should be **total fixed earnings** (gaji pokok + tunjangan tetap), not just gaji pokok — per Perpres 82/2018.

**Fix**: Update `BpjsCalculator` to accept total fixed earnings as base.

**Status**: ✅ UPDATED Task 2.5

---

## 📊 Dependency Chain Verdict

```
VERIFIED CORRECT:
  Phase 0 → everything else                ✅
  Phase 1A → all modules (audit logging)        ✅
  Phase 1B → Phase 2 (employee data for payroll) ✅
  Phase 1C → Phase 2 (violation deductions)      ✅
  Phase 3A → Phase 1D (anti-cheat GPS)           ✅

PARTIALLY CORRECT:
  Phase 3B → Phase 2: SOFT dependency only       ⚠️
  Can decouple 90% of 3B from Phase 2

CAN TRULY PARALLEL:
  Phase 1C and 1D: YES ✅
  - 1C: violations tables, ViolationService, AttendanceController (post-save)
  - 1D: employee_devices, AntiCheatService, AttendanceController (pre-face), face_service.py
  - Only shared touchpoint: AttendanceController (different parts of flow)
```

---

## 🎯 Recommended Execution Order (Revised)

```
Week 1:     Phase 0 → Phase 1A (audit log)
Week 2-3:   Phase 1B (employee data)
            + Phase 3B.1-3B.3 (leave/WFA core — decoupled)  ← PARALLEL
Week 3-4:   Phase 1C (violations) ‖ Phase 1D (anti-cheat)   ← PARALLEL
Week 4-7:   Phase 2 (payroll + THR) + wire 3B unpaid leave
Week 7-8:   Phase 3A (visits) ‖ Phase 3C (dashboard) ‖ Phase 3D (export)
```

**Time Saved**: ~1 week by decoupling Phase 3B from Phase 2

---

## Summary Scorecard

| Area | Verdict | Notes |
|------|---------|-------|
| Dependency ordering | ✅ Mostly correct | Decouple 3B for efficiency |
| PPh 21 TER logic | ✅ Correct | Add mid-year join handling |
| PTKP derivation | ✅ Correct | — |
| BPJS rates | ✅ Correct | Note JP ceiling annual change |
| Anti-cheat GPS | ⚠️ Needs tuning | Adjust thresholds (C4, C5) |
| Liveness detection | ⚠️ Weak | Upgrade to MiniFASNet (C6) |
| Database design | ✅ Sound | Fix employee_salaries duplication (C11) |
| Data migration | 🚨 Missing | CRITICAL 1 — must add |
| Proration | 🚨 Missing | CRITICAL 2 — must add |
| THR | 🚨 Missing | CRITICAL 3 — must add |
| Payroll architecture | ⚠️ Needs work | Queue calculation, fix freeze approach |

**Bottom Line**: Plan demonstrates strong domain knowledge. Fix 3 critical issues, address GPS/liveness concerns, and this is ready for execution.

---

## Implementation Status

- [x] CRITICAL 1: Data migration strategy (Task 1B.6)
- [x] CRITICAL 2: Proration logic (Task 2.4 Step A.5)
- [x] CRITICAL 3: THR calculation (Task 2.12)
- [x] C1-C12: All concerns addressed in plan
- [x] M1-M5: All missing items added
- [x] Dependency graph updated
- [x] Execution order optimized

**Plan Status**: ✅ READY FOR EXECUTION
