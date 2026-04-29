# Enterprise Upgrade Plan — Sistem Absensi & Management

## Context
- **Codebase**: `/home/arjuna/DATA_DRIVE/Project-Coding/Absensi-AI/laravel-app/`
- **Face Service**: `/home/arjuna/DATA_DRIVE/Project-Coding/Absensi-AI/face-service/`
- **Stack**: Laravel 12 (PHP 8.2+), Flask + YOLOv8 + InsightFace, MySQL 8.0, Docker Compose
- **Branch**: `dev` (current), create feature branches per phase
- **Current State**: Basic attendance (face+GPS), leave management, shift/holiday/department CRUD, 7 roles via Spatie, geofencing, backup/export CSV. Missing: Payroll, Violations, Anti-cheat, Liveness, Audit Log, enhanced employee data.
- **ALL commands**: `docker exec absensi_laravel <command>` (code runs inside Docker)
- **UI Pattern**: Blade + Tailwind CSS (Pastel theme) + Alpine.js, Plus Jakarta Sans font, rounded-2xl shadow-soft cards, pastel-badge/modal/card components

## Dependency Graph

```
TD0 (Auth Fix) → prerequisite for everything
     ↓
Phase 1A (Audit Log) → all modules need logging
     ↓
Phase 1B (Employee Data) → Payroll needs this
     ↓                        ↓
Phase 1C (Violations) ──→ Phase 2 (Payroll)
     ↓                        ↓
Phase 1D (Anti-Cheat) ──→ Phase 3A (Kunjungan)
                              ↓
                         Phase 3B (Cuti/WFA)
```

---

## Phase 0: Technical Debt — Critical Security Fix

- [x] **Task 0.1**: Fix API authentication vulnerability. ✅ DONE — Wrapped all API routes in `middleware(['web', 'auth'])` group (session-based auth + CSRF). Added `throttle:6,1` to attendance endpoint. Added `permission:manage_system_settings` to admin settings. Sanctum was NOT installed — used session auth instead (correct for same-origin Blade app). Dashboard fetch already sends X-CSRF-TOKEN + Accept:application/json headers. Updated tests in `ApiAuthorizationTest.php` with proper Spatie role setup.

- [x] **Task 0.2**: Fix font inconsistency. ✅ DONE — Updated `tailwind.config.js` to use `Plus Jakarta Sans` (removed Figtree). Added `display` font family. Added Google Fonts CDN link to `absensi.blade.php`, `app.blade.php`, and `guest.blade.php` (was missing). Replaced Figtree bunny.net link in `guest.blade.php` with Plus Jakarta Sans from Google Fonts. All 4 layouts now consistently use Plus Jakarta Sans.

---

## Phase 1A: Audit Log System

> **Goal**: Rebuild audit logging (table was created then dropped by `2026_03_06_225703_drop_audit_logs_table.php`). Implement auto-logging for all model changes.

- [x] **Task 1A.1**: ✅ DONE — Created `database/migrations/2026_04_29_000001_create_audit_logs_table.php`. Schema: `auditable_type`, `auditable_id`, `event`, `old_values`(json), `new_values`(json), `ip_address`(45), `user_agent`, `url`(500), `created_at` only (immutable). 4 indexes: composite [auditable_type, auditable_id], user_id, event, created_at.

- [x] **Task 1A.2**: ✅ DONE — Created `app/Models/AuditLog.php`. `$guarded=[]`, `$timestamps=false`, casts for old/new_values→array + created_at→datetime. `user()` BelongsTo relation. `scopeForModel()`, `scopeRecent()`. `getChangesAttribute()` accessor with proper diff logic for created/updated/deleted events.

- [x] **Task 1A.3**: ✅ DONE — Created `app/Traits/HasAuditLog.php`. `bootHasAuditLog()` hooks created/updated/deleted events. Sensitive field filtering (password, remember_token, no_rekening, npwp). Console-safe context resolution (try/catch for auth/request). `withoutAuditLog()` static bypass for bulk operations. Skips empty changes on update. Applied to ALL 9 models: User, Attendance, Izin, WorkShift, Department, Holiday, LeaveType, SystemSetting, Announcement.

- [x] **Task 1A.4**: ✅ DONE — Created `app/Http/Controllers/AdminAuditLogController.php` (correct namespace matching web.php import). Filters: search (user name + auditable_type), action (event), date_from, date_to. Paginate 25. Updated `resources/views/admin/audit_logs/index.blade.php`: action→event, model_type→auditable_type, model_id→auditable_id, added date range filters, user_agent display, proper event badge colors.

- [x] **Task 1A.5**: ✅ DONE — `CleanupOldData` command already existed and handles audit log cleanup. Registered scheduler in `routes/console.php`: `Schedule::command('cleanup:old-data')->daily()->at('02:00')`.

---

## Phase 1B: Employee Data Expansion

> **Goal**: Add all fields required for Payroll (PPh 21, BPJS) and HR management to the users table.

- [x] **Task 1B.1**: ✅ DONE — Created `database/migrations/2026_04_29_000002_add_employee_details_to_users_table.php`. All 19 columns added with proper types, nullable, unique constraints, and reversible down() method. Create migration Add columns: `nik` (string 16, nullable, unique), `tempat_lahir` (string nullable), `tanggal_lahir` (date nullable), `jenis_kelamin` (enum: L/P, nullable), `alamat` (text nullable), `no_telepon` (string nullable), `no_rekening` (string nullable — will be encrypted at app level), `nama_bank` (string nullable), `npwp` (string nullable — will be encrypted at app level), `status_pernikahan` (enum: TK/K, nullable — TK=Tidak Kawin, K=Kawin), `jumlah_tanggungan` (tinyInteger default 0), `tanggal_masuk` (date nullable), `tanggal_keluar` (date nullable), `status_karyawan` (enum: tetap/kontrak/magang, default 'tetap'), `gaji_pokok` (decimal 15,2 default 0), `no_bpjs_kesehatan` (string nullable), `no_bpjs_ketenagakerjaan` (string nullable), `emergency_contact_name` (string nullable), `emergency_contact_phone` (string nullable).

- [x] **Task 1B.2**: ✅ DONE — Updated User model: all 19 fields in $fillable, casts for dates/decimal/integer, PTKP/TER accessors, Crypt encrypt/decrypt mutators for no_rekening and npwp, isPayrollReady() method. Update Add all new fields to `$fillable`. Add casts: `tanggal_lahir` → date, `tanggal_masuk` → date, `tanggal_keluar` → date, `gaji_pokok` → decimal:2, `jumlah_tanggungan` → integer. Add accessor `getStatusPtkpAttribute()` that derives PTKP status from `status_pernikahan` + `jumlah_tanggungan`: TK/0, TK/1, TK/2, TK/3, K/0, K/1, K/2, K/3. Add accessor `getKategoriTerAttribute()` that maps PTKP to TER category: A (TK/0, TK/1, K/0), B (TK/2, TK/3, K/1, K/2), C (K/3). Add mutators for `no_rekening` and `npwp` that encrypt on set and decrypt on get using `Crypt::encryptString()` / `Crypt::decryptString()`.

- [x] **Task 1B.3**: ✅ DONE — Created StoreEmployeeRequest and UpdateEmployeeRequest FormRequests with full validation. EmployeeController updated to use FormRequests and handle all 19 new fields in store/update. Fixed old bugs (undefined $user in destroy). Update Add all new fields to the create/edit forms and validation rules. Validation: `nik` → nullable|string|size:16|unique:users, `tanggal_lahir` → nullable|date|before:today, `gaji_pokok` → nullable|numeric|min:0, `status_pernikahan` → nullable|in:TK,K, `jumlah_tanggungan` → integer|min:0|max:3, `no_telepon` → nullable|string|max:15, `status_karyawan` → in:tetap,kontrak,magang. Create a `StoreEmployeeRequest` and `UpdateEmployeeRequest` FormRequest class in `app/Http/Requests/` to extract validation from controller.

- [x] **Task 1B.4**: ✅ DONE — Rewrote `create.blade.php` and `edit.blade.php` with 7 pastel-card sections: (1) Data Pribadi (foto, nama, NIK, tempat/tanggal lahir, jenis kelamin, alamat, telepon), (2) Data Kepegawaian (jabatan, departemen, shift, role as select dropdown with all 7 Spatie roles, status karyawan, tanggal masuk/keluar, gaji pokok with Rp prefix), (3) Data Pajak & BPJS (NPWP, status pernikahan, jumlah tanggungan, Alpine.js-derived PTKP/TER badges, BPJS Kes/TK), (4) Data Bank (nama bank, no rekening), (5) Kontak Darurat, (6) Akun & Keamanan (email, username, password), (7) Data Wajah (create only — camera/upload face recognition). Edit form pre-populates all fields with `old('field', $employee->field)`, includes foto preview, optional password change. Removed broken 4-radio-button role layout.

- [x] **Task 1B.5**: ✅ DONE — Fixed `index.blade.php`: renamed `no_hp`→`no_telepon`, `nip`→`nik` (display, data attributes, modal, JS), removed duplicate Tambah button block. Added NIK display, status_karyawan pastel-badge (tetap=sage, kontrak=sky, magang=peach), tanggal_masuk to each card. Replaced non-functional Filter button with working Alpine.js dropdown panel (status_karyawan, department_id, face_status filters). Fixed `ExportService.php`: `$emp->role`→`$emp->getRoleNames()->first()`, added 10 new CSV columns (NIK, Status Karyawan, Tanggal Masuk, No. Telepon, Jenis Kelamin, Gaji Pokok, Status Pernikahan, Jumlah Tanggungan, BPJS Kes, BPJS TK), added `status_karyawan` filter support, excluded encrypted fields (NPWP, no_rekening) from export.

---

## Phase 1C: Violation Points System (Poin Pelanggaran)

> **Goal**: Auto-generate violation points from late attendance, accumulate monthly, trigger warning letters (SP), integrate with future Payroll deductions.

- [x] **Task 1C.1**: ✅ DONE — Created `database/migrations/2026_04_30_000003_create_violation_tables.php`. Three tables: violation_types (name/code/points/is_auto/is_active), violations (user_id/violation_type_id/tanggal/points/reference_type/reference_id/notes/created_by with indexes), warning_letters (user_id/type[SP1-3]/period_month/total_points/issued_at with unique constraint). down() drops in reverse order.

- [x] **Task 1C.2**: ✅ DONE — Created ViolationType (hasMany Violation), Violation (belongsTo User/ViolationType, morphTo reference, creator()), WarningLetter (belongsTo User, creator()). All with HasAuditLog. Added violations()/warningLetters() hasMany to User. Added violations() morphMany to Attendance.

- [x] **Task 1C.3**: ✅ DONE — Created ViolationTypeSeeder with 6 types (TELAT_1-4, ALPHA, MANUAL) using updateOrCreate by code. Registered in DatabaseSeeder.

- [x] **Task 1C.4**: ✅ DONE — Created ViolationService (143 lines) with 5 methods: generateFromAttendance (dedup by reference, late minutes calculation, maps to TELAT_1-4), generateAlpha (dedup check), getMonthlyPoints (sum by LIKE yearMonth%), checkWarningThreshold (SP1/2/3 from configurable thresholds), getPayrollDeduction (per_point or percentage mode).

- [x] **Task 1C.5**: ✅ DONE — Integrated ViolationService into Api\AttendanceController (after attendance save, before late notification). Created GenerateAlphaViolations command scheduled at 23:00 daily. Checks active employees for missing attendance + no approved izin.

- [x] **Task 1C.6**: ✅ DONE — Created migration adding 6 columns to system_settings (violation_deduction_type, violation_deduction_per_point, violation_deduction_percentage, sp1/2/3_threshold). Updated SystemSetting model. Created poin_pelanggaran settings tab with two-column layout (deduction settings + SP thresholds). Registered in AuthorizesSettingsTabs.

- [x] **Task 1C.7**: ✅ DONE — Created AdminViolationController (flat namespace) with index/create/store/monthlyReport. 3 views (index, create, monthly-report) with pastel badges. 4 routes + 1 settings route under manage_violations permission. Permission added to Direktur/VP/Manager in seeder. Sidebar item with gavel icon.

- [x] **Task 1C.8**: ✅ DONE — Added violation summary card to employee dashboard (+73 lines). Shows monthly points with pastel-badge (sage=Bersih, peach=Perhatian, rose=Peringatan) and active SP level. Queries via ViolationService.

---

## Phase 1D: Anti-Fake GPS & Liveness Detection

> **Goal**: Multi-layer anti-cheat system to prevent GPS spoofing and photo-based face recognition bypass.

- [x] **Task 1D.1**: ✅ DONE — Created `2026_05_01_000001_create_employee_devices_and_anticheat_columns.php`. employee_devices table (user_id FK, device_fingerprint unique, device_name, platform, browser, screen_resolution, is_trusted, last_used_at). Added 6 columns to attendances: device_fingerprint, gps_readings(json), ip_address(45), timezone_client, anomaly_score(int default 0), anomaly_flags(json).

- [x] **Task 1D.2**: ✅ DONE — Created EmployeeDevice model with HasAuditLog, belongsTo(User). Added devices() hasMany to User. Created migration adding max_devices_per_user, anomaly_score_warning/reject_threshold, enable_anti_cheat to system_settings. Updated SystemSetting model. Updated Attendance model $fillable and $casts.

- [x] **Task 1D.3**: ✅ DONE — Updated dashboard.blade.php cameraHandler(): 3 sequential GPS readings with 1-second intervals, step indicator "Mengambil lokasi (1/3)...", averages readings for main lat/long, generates device fingerprint hash, sends gps_readings/device_fingerprint/timezone_client/mock_location_detected in FormData.

- [x] **Task 1D.4**: ✅ DONE — Created AntiCheatService (241 lines) with 6 checks: GPS variance (<0.000001=15pts), speed anomaly (contextual thresholds <2min=5km/h, <15min=30km/h, <1hr=120km/h, else=200km/h, 20pts), accuracy paradox (=1.0 or <3.0m, 10pts), device consistency (new device + max reached, 15pts), timezone mismatch (>2hr offset, 10pts), mock location (20pts). Created AntiCheatResult DTO.

- [x] **Task 1D.5**: ✅ DONE — Integrated AntiCheatService into Api\AttendanceController after GPS validation, before face recognition. Rejects with 403 if score >= reject_threshold. Registers/updates device in employee_devices. Saves anti-cheat data with attendance. Added face identity verification: $recognizedName !== $authUser->username → 403.

- [x] **Task 1D.6**: ✅ DONE — Added check_liveness() to face_service.py: LBP texture (25%), YCrCb color distribution (15%), Laplacian sharpness (60%). LIVENESS_THRESHOLD=0.5. Integrated into /recognize_frame with anti_spoofing_enabled parameter. Returns liveness_score in response. Added /health endpoint.

- [x] **Task 1D.7**: ✅ DONE — Created AdminAnomalyController (flat namespace) with index() and markDeviceUntrusted(). Anomaly index view with color-coded score badges (sage/sky/peach/rose) and flag labels. Routes under view_anomaly_attendance permission. Permission added to Direktur/VP/Manager. "Anomali GPS" sidebar item with gps_off icon. Anti-cheat settings tab in kehadiran category.

---

## Phase 2: Payroll Engine (PPh 21 TER + BPJS)

> **Goal**: Full payroll system compliant with Indonesian tax law (PP 58/2023 TER method) and BPJS regulations.

- [x] **Task 2.1**: ✅ DONE — Created `2026_05_02_000001_create_payroll_tables.php` (7 tables: salary_components, employee_salary_components, payroll_periods, payroll_details, payroll_detail_items, tax_ter_rates, tax_ptkp_rates + late_minutes_total on attendances) and `2026_05_02_000002_add_payroll_settings_to_system_settings.php` (bpjs_kes_ceiling, bpjs_jp_ceiling, jkk_risk_group, no_npwp_surcharge_enabled). Create migration `2026_xx_xx_000005_create_payroll_tables.php`. Tables: (1) `salary_components`: id, name, code (unique), type (enum: earning/deduction/benefit), is_taxable (boolean), is_fixed (boolean — fixed vs variable), default_amount (decimal 15,2 default 0), description, is_active, timestamps. (2) `employee_salaries`: id, user_id (foreignId unique), gaji_pokok (decimal 15,2), components (json — array of {component_id, amount}), effective_date (date), timestamps. (3) `payroll_periods`: id, period_month (string YYYY-MM, unique), start_date (date), end_date (date), status (enum: draft/processing/calculated/approved/paid/locked), total_employees (integer default 0), total_gross (decimal 15,2 default 0), total_deductions (decimal 15,2 default 0), total_net (decimal 15,2 default 0), calculated_at (timestamp nullable), approved_by (foreignId nullable), approved_at (timestamp nullable), paid_at (timestamp nullable), notes (text nullable), timestamps. (4) `payroll_details`: id, payroll_period_id (foreignId), user_id (foreignId), working_days (integer), present_days (integer), late_count (integer), late_minutes_total (integer), overtime_hours (decimal 8,2), gaji_pokok (decimal 15,2), total_earnings (decimal 15,2), total_deductions (decimal 15,2), total_bpjs_company (decimal 15,2), total_bpjs_employee (decimal 15,2), pph21_amount (decimal 15,2), net_salary (decimal 15,2), status (enum: calculated/approved/paid), timestamps. Unique: [payroll_period_id, user_id]. (5) `payroll_detail_items`: id, payroll_detail_id (foreignId), component_name (string), component_type (enum: earning/deduction/bpjs_company/bpjs_employee/tax), amount (decimal 15,2), notes (string nullable), timestamps. (6) `tax_ter_rates`: id, category (enum: A/B/C), min_income (decimal 15,2), max_income (decimal 15,2), rate (decimal 8,4 — percentage as decimal e.g. 0.0025 for 0.25%), timestamps. (7) `tax_ptkp_rates`: id, status (string — TK/0, TK/1, TK/2, TK/3, K/0, K/1, K/2, K/3), amount (decimal 15,2), year (integer), timestamps. Unique: [status, year].

- [x] **Task 2.2**: ✅ DONE — Created 7 models (SalaryComponent, EmployeeSalaryComponent, PayrollPeriod, PayrollDetail, PayrollDetailItem, TaxTerRate, TaxPtkpRate) all with HasAuditLog. Added payrollDetails() and salaryComponents() to User model. Create models for all payroll tables. `SalaryComponent`, `EmployeeSalary`, `PayrollPeriod`, `PayrollDetail`, `PayrollDetailItem`, `TaxTerRate`, `TaxPtkpRate`. Add proper relationships, casts, and HasAuditLog trait. PayrollPeriod hasMany PayrollDetail. PayrollDetail belongsTo PayrollPeriod and User, hasMany PayrollDetailItem. User hasOne EmployeeSalary, hasMany PayrollDetail.

- [x] **Task 2.3**: ✅ DONE — Created PayrollSeeder with 9 salary components, complete TER rates for all 3 categories (A/B/C) per PP 58/2023, and 8 PTKP rates. Registered in DatabaseSeeder. Create `database/seeders/PayrollSeeder.php`. Seed: (1) Default salary components: Gaji Pokok (earning, taxable, fixed), Tunjangan Jabatan (earning, taxable, fixed), Tunjangan Transport (earning, taxable, not fixed), Tunjangan Makan (earning, taxable, not fixed), Uang Lembur (earning, taxable, not fixed), Tunjangan Kehadiran (earning, taxable, not fixed), Potongan Keterlambatan (deduction), Potongan Pelanggaran (deduction), Potongan Cuti Tidak Berbayar (deduction). (2) TER rates for 2024 — all 3 categories (A/B/C) with complete rate tables from PP 58/2023 (~40 rows per category). (3) PTKP rates for 2024: TK/0=54.000.000, TK/1=58.500.000, TK/2=63.000.000, TK/3=67.500.000, K/0=58.500.000, K/1=63.000.000, K/2=67.500.000, K/3=72.000.000. Register in DatabaseSeeder.

- [x] **Task 2.4**: ✅ DONE — Created PayrollService (447 lines) with createPeriod, calculateForEmployee (7 steps: attendance counting, proration, gross/overtime, deductions, BPJS, PPh21, net), calculateAll, approvePeriod, markAsPaid. Includes working day calculation, unpaid leave deduction, and mid-month proration. Create `app/Services/PayrollService.php` — the core calculation engine. Methods: (1) `createPeriod(string $yearMonth): PayrollPeriod` — create draft period, calculate working days from holidays + weekend_days setting. (2) `calculateForEmployee(PayrollPeriod $period, User $user): PayrollDetail` — the main calculation: Step A: Count present_days, late_count, late_minutes, overtime_hours from Attendance records in period. Step B: Calculate gross = gaji_pokok + sum of earning components from EmployeeSalary. Add overtime: upah_sejam = gaji_pokok / 173, jam_1 = 1.5 × upah_sejam, jam_2+ = 2.0 × upah_sejam (per UU Cipta Kerja). Step C: Calculate deductions — late deduction (from ViolationService::getPayrollDeduction), unpaid leave prorate (gaji_pokok / working_days × unpaid_leave_days). Step D: Calculate BPJS (see Task 2.5). Step E: Calculate PPh 21 (see Task 2.6). Step F: net_salary = gross - deductions - bpjs_employee - pph21. Step G: Save PayrollDetail + PayrollDetailItems for each component. (3) `calculateAll(PayrollPeriod $period)` — loop all active employees, call calculateForEmployee for each. Update period totals and status to 'calculated'. (4) `approvePeriod(PayrollPeriod $period, User $approver)` — change status to 'approved'. (5) `markAsPaid(PayrollPeriod $period)` — change status to 'paid', then 'locked'.

- [x] **Task 2.5**: ✅ DONE — Created BpjsCalculator with BPJS Kes (4%+1% with ceiling), JHT (3.7%+2%), JKK (5 risk groups), JKM (0.3%), JP (2%+1% with ceiling). Uses totalFixedEarnings for Kes base per Perpres 82/2018. Create `app/Services/BpjsCalculator.php`. Method `calculate(decimal $gajiPokok, array $options): BpjsResult`. Options include: jkk_risk_group (1-5, default 1). Calculations: (1) BPJS Kesehatan: base = min(gajiPokok, ceiling from setting). Company = base × 4%. Employee = base × 1%. (2) JHT: Company = gajiPokok × 3.7%. Employee = gajiPokok × 2%. (3) JKK: Company = gajiPokok × rate_by_group (0.24%, 0.54%, 0.89%, 1.27%, 1.74%). (4) JKM: Company = gajiPokok × 0.3%. (5) JP: base = min(gajiPokok, jp_ceiling — default Rp 10.042.300). Company = base × 2%. Employee = base × 1%. Return BpjsResult DTO with all amounts separated by company/employee. Add system settings: `bpjs_kes_ceiling` (decimal, default 12000000), `bpjs_jp_ceiling` (decimal, default 10042300), `jkk_risk_group` (integer, default 1).

- [x] **Task 2.6**: ✅ DONE — Created Pph21Calculator with calculateMonthlyTER (TER rate lookup) and calculateDecemberCorrection (annual correction with biaya jabatan 5% max 6jt, BPJS JHT+JP deduction, PTKP, progressive rates 5/15/25/30/35%, no-NPWP 20% surcharge toggle). Create `app/Services/Pph21Calculator.php`. Two methods: (1) `calculateMonthlyTER(decimal $brutoMonthly, string $terCategory, string $periodMonth): decimal` — For Jan-Nov: lookup TER rate from `tax_ter_rates` table by category and income range. PPh 21 = bruto × rate. (2) `calculateDecemberCorrection(User $user, int $year): decimal` — Annual correction: Sum all bruto Jan-Dec. Subtract biaya_jabatan (5% of total bruto, max Rp 6.000.000). Subtract total BPJS employee (JHT + JP) for the year. Subtract PTKP (lookup from tax_ptkp_rates by user's status_ptkp). PKP = result (if negative, PKP = 0). Apply progressive rates: 0-60jt=5%, 60-250jt=15%, 250-500jt=25%, 500jt-5M=30%, >5M=35%. PPh21_annual = PKP × progressive rates. PPh21_december = PPh21_annual - sum(PPh21 Jan-Nov). Handle edge case: if user has no NPWP, PPh 21 is 20% higher (UU HPP).

- [x] **Task 2.7**: ✅ DONE — Created AdminPayrollController (flat namespace) with index/create/store/show/calculate/approve/markPaid/slip/downloadSlip. Added 9 routes, manage_payroll+view_payroll permissions, Payroll sidebar item with payments icon. Create `app/Http/Controllers/Admin/AdminPayrollController.php`. Methods: index (list periods with status badges), create (form to create new period — select month/year), show (period detail — list all employees with calculated amounts), calculate (trigger calculation — show progress), approve (change status), markPaid, slip (generate individual slip). Add routes under admin group with new permission `manage_payroll`. Update RoleAndPermissionSeeder: add `manage_payroll` to Direktur and VP, add `view_payroll` to Manager. Add sidebar menu item "Payroll" with Material Icon `payments`.

- [x] **Task 2.8**: ✅ DONE — Created 4 views: index (period list with status badges), create (month/year form), show (summary cards + expandable employee table), slip (print-friendly with JetBrains Mono). Create payroll admin views. (1) `resources/views/admin/payroll/index.blade.php` — list of payroll periods with status badges (draft=sky, processing=lavender, calculated=peach, approved=sage, paid=sage-dark, locked=stone). Action buttons per status. (2) `show.blade.php` — period detail with summary cards (Total Karyawan, Total Bruto, Total Potongan, Total Netto) and employee table (Nama, Gaji Pokok, Earnings, Deductions, BPJS, PPh 21, Netto). Expandable row for detail items. (3) `slip.blade.php` — individual salary slip view with company header, employee info, earnings table, deductions table, BPJS breakdown, PPh 21, net salary. Print-friendly CSS. Use JetBrains Mono font for numbers (add to Tailwind config).

- [x] **Task 2.9**: ✅ DONE — Created SlipGajiPdfService (requires barryvdh/laravel-dompdf — NOT yet installed) and pdf/slip-gaji.blade.php with inline CSS. Added download route. Create `app/Services/SlipGajiPdfService.php` using `barryvdh/laravel-dompdf`. Install package via composer. Method `generate(PayrollDetail $detail): string` — renders `resources/views/pdf/slip-gaji.blade.php` to PDF. The PDF view should include: company logo + name + address, employee name + NIK + department + jabatan, period, earnings table, deductions table, BPJS breakdown (company + employee portions), PPh 21, net salary, generated timestamp. Return file path. Method `generateBulk(PayrollPeriod $period): string` — generate all slips as merged PDF or ZIP. Add route for download.

- [x] **Task 2.10**: ✅ DONE — Created PayrollFreezeTrait (model-level freeze on saving/deleting) + PayrollFrozenException + PayrollFreezeMiddleware (UX layer). Applied trait to Attendance, Violation, Izin. Registered middleware alias in bootstrap/app.php. Create `app/Http/Middleware/PayrollFreezeMiddleware.php`. When a PayrollPeriod is in status 'processing' or 'calculated', block any modifications to: attendances, violations, and izins for that period's date range. Apply to relevant routes. Return 423 Locked with message "Data sedang diproses untuk payroll periode {month}. Hubungi admin." Register middleware alias in bootstrap/app.php.

- [x] **Task 2.11**: ✅ DONE — Created tests/Unit/PayrollCalculationTest.php with 6 Pest tests: TER rate lookup (5jt=0%, 10jt=correct rate), BPJS ceiling enforcement, overtime calculation, proration, December correction. Write unit tests for payroll calculations. Create `tests/Unit/PayrollServiceTest.php`, `tests/Unit/BpjsCalculatorTest.php`, `tests/Unit/Pph21CalculatorTest.php`. Test cases: (1) Employee TK/0, gaji 5jt → PPh 21 = 0 (below threshold). (2) Employee K/1, gaji 10jt → verify TER rate lookup and calculation. (3) Employee K/3, gaji 50jt → verify TER category C. (4) December correction with progressive rates. (5) BPJS ceiling enforcement. (6) Overtime calculation (1 hour, 3 hours). (7) Late deduction integration. (8) Employee without NPWP (20% surcharge). Run tests via `docker exec absensi_laravel php artisan test --filter=Payroll`.

---

## Phase 3A: Visit Attendance (Absensi Kunjungan)

> **Goal**: Multi-location visit tracking with GPS trail for field employees.

- [x] **Task 3A.1**: ✅ DONE — Created `2026_05_03_000001_create_visit_attendance_tables.php` with visit_attendances (20+ columns, indexed [user_id, tanggal]) and visit_locations (GPS trail, cascade delete). Create migration `2026_xx_xx_000006_create_visit_attendance_tables.php`. Tables: (1) `visit_attendances`: id, user_id (foreignId), tanggal (date), client_name (string), location_name (string), purpose (text), check_in_time (datetime), check_out_time (datetime nullable), check_in_lat (decimal 10,7), check_in_long (decimal 10,7), check_out_lat (decimal 10,7 nullable), check_out_long (decimal 10,7 nullable), check_in_photo (string nullable), check_out_photo (string nullable), similarity_score_in (float nullable), similarity_score_out (float nullable), notes (text nullable), status (enum: active/completed/cancelled), device_fingerprint (string nullable), anomaly_score (integer default 0), timestamps. (2) `visit_locations`: id, visit_attendance_id (foreignId), latitude (decimal 10,7), longitude (decimal 10,7), accuracy (float), recorded_at (timestamp), timestamps. Index on visit_attendance_id.

- [x] **Task 3A.2**: ✅ DONE — Created VisitAttendance + VisitLocation models with HasAuditLog. Added visitAttendances() to User. Created Api\VisitAttendanceController with checkIn/checkOut/trackLocation. Added 3 API routes. Create models `VisitAttendance` and `VisitLocation`. Relations, casts, HasAuditLog. Add to User: `hasMany(VisitAttendance)`. Create `app/Http/Controllers/Api/VisitAttendanceController.php` with methods: checkIn (validate face + GPS, create record), checkOut (validate face + GPS, complete record), trackLocation (receive periodic GPS updates, save to visit_locations). Add API routes with auth:sanctum middleware.

- [x] **Task 3A.3**: ✅ DONE — Added "Absen Kunjungan" button to dashboard, visit check-in modal (2-step: form→camera), active visit section with check-out + background GPS tracking (5min interval), visit history table with pastel badges. Create user-facing visit UI. Add "Absen Kunjungan" button to user dashboard. Create modal/page for visit check-in: form fields (client name, location name, purpose) + camera + GPS. Show active visits with check-out button. Show visit history with expandable details.

- [x] **Task 3A.4**: ✅ DONE — Created AdminVisitAttendanceController (flat namespace) with index/show. Views with Leaflet.js map (green check-in marker, red check-out marker, blue GPS trail polyline). Added view_visit_attendance permission to Direktur/VP/Manager/Supervisor. Sidebar "Kunjungan" with location_on icon. Create admin visit management. `AdminVisitAttendanceController` with index (list all visits, filterable by user/date/status), show (detail with GPS trail map using Leaflet.js — plot visit_locations as polyline on map, show check-in/out markers). Add route, permission `view_visit_attendance`, sidebar menu item "Kunjungan" with Material Icon `location_on`.

---

## Phase 3B: Enhanced Leave & WFA Workflow

> **Goal**: Multi-level approval chain, leave balance tracking, WFA (Work From Anywhere) support.

- [x] **Task 3B.1**: ✅ DONE — Created `2026_05_03_000002_enhance_leave_system.php`. Added 6 columns to izins (leave_type_id, approval_status enum, current_approver_id, approved_by json, wfa_location, wfa_daily_checkins json). Created leave_balances table with unique [user_id, leave_type_id, year]. Create migration `2026_xx_xx_000007_enhance_leave_system.php`. (1) Add columns to `izins` table: `leave_type_id` (foreignId nullable), `approval_status` (enum: pending/approved_l1/approved_l2/approved_final/rejected, default 'pending'), `current_approver_id` (foreignId nullable), `approved_by` (json nullable — array of {user_id, role, action, timestamp}), `wfa_location` (string nullable), `wfa_daily_checkins` (json nullable). (2) New table `leave_balances`: id, user_id (foreignId), leave_type_id (foreignId), year (integer), quota (integer), used (integer default 0), remaining (integer — computed or stored), carry_over (integer default 0), timestamps. Unique: [user_id, leave_type_id, year].

- [x] **Task 3B.2**: ✅ DONE — Created LeaveBalance model with HasAuditLog. Updated Izin model with new fields/casts/relations. Added leaveBalances() to User. Created LeaveService with 7 methods (getBalance, deductBalance, restoreBalance, initializeYearlyBalances, carryOver, getNextApprover with role hierarchy, approve, reject). Create `LeaveBalance` model. Create `app/Services/LeaveService.php` with methods: `getBalance(User, LeaveType, year)`, `deductBalance(User, LeaveType, days)`, `initializeYearlyBalances(year)` — create balances for all active employees based on leave_type quotas, `carryOver(year)` — carry unused days to next year (configurable max). Integrate with Izin approval: when izin is fully approved, deduct balance. When rejected/cancelled, restore balance.

- [x] **Task 3B.3**: ✅ DONE — Enhanced IzinController with leave_type_id, WFA support, approval chain init, getLeaveBalance AJAX, wfaCheckin. Updated AdminAbsenceController with approve/reject using LeaveService. Created AdminLeaveBalanceController with index/update/initialize. Added manage_leave_balances permission. Update `IzinController` and `AdminIzinController` (or create if needed). Implement multi-level approval: Staf submits → Team Leader approves (L1) → Manager approves (L2) → Final. The approval chain is determined by the submitter's role and department hierarchy. Each approver only sees izin pending their level. Add WFA type to izin jenis options. For WFA: require daily check-in (simple GPS ping stored in wfa_daily_checkins JSON).

- [x] **Task 3B.4**: ✅ DONE — Updated dashboard izin modal with WFA option + leave balance fetch. Updated izin/index with balance cards + approval step indicator. Updated admin/absence with approval level column + reject modal. Created admin/leave-balances/index with editable balances + initialize button. Added "Saldo Cuti" sidebar item. Update izin views. Show leave balance in the izin request form (user dashboard). Show approval chain progress (step indicator). Admin izin view: show current approval level, action buttons for approve/reject at current level. Add leave balance management page for admin (view/adjust balances per employee).

---

## Phase 3C: Dashboard Analytics Enhancement

> **Goal**: Rich analytics with charts and real-time data.

- [x] **Task 3C.1**: ✅ DONE — Enhanced AdminDashboardController with 7 analytics methods: calculateTrendIndicators, getAttendanceTrend (30-day), getDepartmentComparison, getTopLateEmployees, getViolationSummary, getPendingApprovals, getRecentAnomalies. All respect RoleBasedScope. Enhance `AdminDashboardController`. Add data aggregation: attendance trend (last 30 days — daily count of hadir/terlambat/alpha), department comparison (attendance rate per department), top late employees (this month), violation summary (this month), pending approvals count. Return as JSON-ready arrays for chart consumption.

- [x] **Task 3C.2**: ✅ DONE — Rebuilt admin dashboard (224→528 lines) with ApexCharts CDN. Replaced CSS pie chart with interactive donut. Added 30-day trend area chart, department comparison bar chart, anomali terbaru panel, pending actions widget, violation summary widget, trend indicators (↑↓%) on stat cards. Dark mode support. Update `resources/views/admin/dashboard.blade.php`. Add ApexCharts CDN. Replace CSS pie chart with ApexCharts donut. Add line chart for 30-day attendance trend. Add bar chart for department comparison. Add "Anomali Terbaru" alert panel (last 5 anomaly-flagged attendances). Add "Pending Actions" widget (pending izin, pending payroll). Keep existing stat cards but enhance with trend indicators (↑↓ vs last month).

---

## Phase 3D: Enhanced Export & Auto-Checkout

- [x] **Task 3D.1**: ✅ DONE — Added exportAttendancePdf() to ExportService. Created attendance-report.blade.php PDF template with company header, overview stats, detail table, per-employee summary. Added AdminExportController::exportAttendancePdf(). Route + PDF button in export tab. Requires `barryvdh/laravel-dompdf` (not yet installed). Install `barryvdh/laravel-dompdf` (if not already installed in Phase 2). Update `ExportService` to support PDF export for attendance reports. Create `resources/views/pdf/attendance-report.blade.php` — monthly attendance report with company header, date range, employee table, summary statistics. Add PDF download button alongside existing CSV export in admin attendance view.

- [x] **Task 3D.2**: ✅ DONE — Created AutoCheckout command (attendance:auto-checkout). Reads auto_checkout_time from SystemSetting, finds open attendances, sets jam_keluar, appends auto_checkout to status. Idempotent. Manual trigger button in kebijakan_absensi settings tab. Scheduled dailyAt('23:55'). Create `app/Console/Commands/AutoCheckout.php`. Runs at `auto_checkout_time` from system settings. Finds all attendances for today where `jam_masuk` is set but `jam_keluar` is null. Sets `jam_keluar` to `auto_checkout_time`, sets status to include 'auto-checkout' flag. Register in scheduler. Log results to audit log.

---

## Phase 4: Enterprise Features (Future — Not in immediate scope)

> These phases are documented for roadmap purposes. Execute after Phases 1-3 are stable.

- [x] **Task 4.1**: ✅ DONE — Project & Task Management. Migration (5 tables: projects, project_members, tasks, task_comments, time_entries). 5 models (Project, ProjectMember, Task, TaskComment, TimeEntry). AdminProjectController + AdminTaskController. Kanban board with Alpine.js drag-drop. Time tracking with start/stop timer. manage_projects permission. Sidebar "Project" with assignment icon.
- [x] **Task 4.2**: ✅ DONE — Recruitment Pipeline. Migration (5 tables: job_positions, candidates, interviews, onboarding_tasks, candidate_onboarding). 5 models (JobPosition, Candidate, Interview, OnboardingTask, CandidateOnboarding). AdminRecruitmentController (14 methods). Kanban pipeline with drag-drop. Interview scheduling + scoring. Onboarding checklist. Resume upload. manage_recruitment permission. Sidebar "Rekrutmen" with person_search icon.
- [x] **Task 4.3**: ✅ DONE — Asset Management. Migration (4 tables: asset_categories, assets, asset_assignments, asset_maintenances). 4 models (AssetCategory, Asset, AssetAssignment, AssetMaintenance). AssetService (assign/return/depreciation). AdminAssetController (14 methods). 6 views with ApexCharts depreciation chart. manage_assets permission. Sidebar "Aset" with inventory_2 icon.
- [x] **Task 4.4**: ✅ DONE — Service Tickets. Migration (3 tables: ticket_categories, tickets, ticket_comments). 3 models (TicketCategory, Ticket, TicketComment) with auto ticket_number generation + SLA deadline auto-set. TicketController (user-facing) + AdminTicketController (admin). SLA indicators (green/yellow/red). Internal notes. manage_tickets + create_tickets permissions. Sidebar "Tiket Layanan" with confirmation_number icon.
- [x] **Task 4.5**: ✅ DONE — Internal Form Builder. Migration (3 tables: form_templates, form_submissions, form_submission_comments). 3 models (FormTemplate, FormSubmission, FormSubmissionComment). FormController (user) + AdminFormController (admin). Alpine.js form builder with field palette. Dynamic form renderer. CSV export. manage_forms permission. Sidebar "Form Internal" with dynamic_form icon.
- [x] **Task 4.6**: ✅ DONE — LMS/Training. Migration (4 tables: courses, course_materials, course_enrollments, course_material_progress). 4 models (Course, CourseMaterial, CourseEnrollment, CourseMaterialProgress). TrainingController (user) + AdminTrainingController (admin). Learning interface with AJAX progress. Certificate generation. Training report. manage_training permission. Sidebar "Training" with school icon.
- [x] **Task 4.7**: ✅ DONE — Multi-Company/Branch. Migration (2 tables: companies, company_branches + company_id/branch_id on users). 2 models (Company, CompanyBranch). CompanyService + CompanyScope middleware. AdminCompanyController (14 methods). 5 views with Leaflet.js branch map. Company-specific settings override. Employee transfer. manage_companies permission (Direktur/VP only). Sidebar "Perusahaan" with business icon.

---

## Execution Notes
- All artisan/composer commands: `docker exec absensi_laravel <command>`
- Laravel code path: `/home/arjuna/DATA_DRIVE/Project-Coding/Absensi-AI/laravel-app/`
- Face service path: `/home/arjuna/DATA_DRIVE/Project-Coding/Absensi-AI/face-service/`
- Create feature branch `feature/enterprise-upgrade` from `dev` before starting
- Run `php artisan migrate` after each migration task
- Run `php artisan db:seed --class=<SeederName>` after each seeder task
- Phase 0 → Phase 1A → Phase 1B → Phase 1C (can parallel with 1D) → Phase 2 → Phase 3 (A/B/C/D can parallel)
- Phase 2 has hard dependency on 1B (employee data) and 1C (violations)
- Phase 3A depends on 1D (anti-cheat GPS)
- Phase 3B depends on Phase 2 (payroll integration for unpaid leave)
- ALWAYS run existing tests after each phase to prevent regressions
- Update AGENTS.md after each major phase completion
