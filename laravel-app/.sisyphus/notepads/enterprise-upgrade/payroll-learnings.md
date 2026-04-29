# Payroll Engine — Implementation Learnings

## Architecture Decisions

1. **PayrollService as orchestrator**: The core engine delegates BPJS to `BpjsCalculator` and PPh 21 to `Pph21Calculator`. This separation keeps each service focused and testable.

2. **TER method (PP 58/2023)**: Monthly PPh 21 uses the TER (Tarif Efektif Rata-rata) lookup for Jan-Nov. December uses annual correction with progressive rates (Pasal 17 UU PPh). The TER rates are stored in `tax_ter_rates` table with 44 brackets for Category A, 40 for B, and 41 for C.

3. **BPJS ceiling enforcement**: BPJS Kesehatan uses `totalFixedEarnings` capped at `bpjs_kes_ceiling` (default 12M). JP uses `gajiPokok` capped at `bpjs_jp_ceiling` (default 10.042.300). JHT/JKK/JKM use full `gajiPokok` without ceiling.

4. **Overtime per UU Cipta Kerja**: `upah_sejam = gaji_pokok / 173`. First hour at 1.5x, subsequent hours at 2.0x.

5. **Weekend days mapping**: SystemSetting stores weekend days as English day names (`['Saturday', 'Sunday']`). PayrollService maps these to Carbon dayOfWeek numbers (0=Sunday, 6=Saturday) for working day calculation.

## Key Gotchas

- **NPWP encryption**: User model encrypts NPWP via accessor/mutator. For checking if NPWP exists in Pph21Calculator, use `getRawOriginal('npwp')` to check the raw encrypted value, not the decrypted accessor.

- **December correction BPJS deduction**: Only JHT + JP employee contributions are deductible from annual bruto for PKP calculation, NOT BPJS Kesehatan. The code filters PayrollDetailItems by component_name.

- **Proration**: Applied when `tanggal_masuk` or `tanggal_keluar` falls within the period. Factor = effective_working_days / total_working_days.

- **PayrollFreeze**: Uses a trait on Attendance, Violation, and Izin models. Checks if any PayrollPeriod with status 'processing' or 'calculated' overlaps the record's date. Throws `PayrollFrozenException` to prevent data modification during payroll processing.

## Files Created/Modified

### New Files (14)
- `database/migrations/2026_05_02_000002_add_payroll_settings_to_system_settings.php`
- `app/Services/SlipGajiPdfService.php`
- `app/Http/Controllers/AdminPayrollController.php`
- `app/Traits/PayrollFreezeTrait.php`
- `app/Exceptions/PayrollFrozenException.php`
- `app/Http/Middleware/PayrollFreezeMiddleware.php`
- `resources/views/admin/payroll/index.blade.php`
- `resources/views/admin/payroll/create.blade.php`
- `resources/views/admin/payroll/show.blade.php`
- `resources/views/admin/payroll/slip.blade.php`
- `resources/views/pdf/slip-gaji.blade.php`
- `tests/Unit/PayrollCalculationTest.php`

### Modified Files (10)
- `database/migrations/2026_05_02_000001_create_payroll_tables.php` (fixed is_fixed defaults, added late_minutes_total to attendances)
- `database/seeders/PayrollSeeder.php` (corrected TER rates to match PP 58/2023, fixed salary component codes)
- `app/Services/PayrollService.php` (fixed weekend_days mapping, added unpaid leave deduction, fixed calculateMonthlyTER call signature)
- `app/Services/BpjsCalculator.php` (added options parameter, renamed keys to jkk/jkm)
- `app/Services/Pph21Calculator.php` (removed extra periodMonth param, fixed BPJS deduction in December correction)
- `app/Models/Attendance.php` (added PayrollFreezeTrait)
- `app/Models/Violation.php` (added PayrollFreezeTrait)
- `app/Models/Izin.php` (added PayrollFreezeTrait)
- `database/seeders/RoleAndPermissionSeeder.php` (added manage_payroll, view_payroll permissions)
- `routes/web.php` (added payroll routes)
- `resources/views/layouts/admin.blade.php` (added Payroll sidebar item)
- `bootstrap/app.php` (registered payroll_freeze middleware alias)

### Pre-existing Files (unchanged)
- 7 models (SalaryComponent, EmployeeSalaryComponent, PayrollPeriod, PayrollDetail, PayrollDetailItem, TaxTerRate, TaxPtkpRate)
- User model (already had payroll relationships)
- SystemSetting model (already had BPJS fields)
- DatabaseSeeder (already called PayrollSeeder)

## Dependencies
- `barryvdh/laravel-dompdf` — Required for PDF slip generation. Must be installed via `composer require barryvdh/laravel-dompdf`.
