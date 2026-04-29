# Dashboard Analytics Enhancement — Learnings

## Phase 3C Implementation Notes

### Architecture Decisions

- **Controller stays flat**: All analytics methods are private helpers on `AdminDashboardController` rather than a separate service class. The controller went from 78 to ~342 lines — still manageable but approaching the threshold where extraction to a `DashboardAnalyticsService` would be warranted.
- **RoleBasedScope applied consistently**: Every analytics query that touches attendance or izin data goes through `RoleBasedScope` so managers/supervisors only see their department's data. Department comparison is the exception — it shows all departments since it's aggregate-level data.
- **Trend normalization**: Month-over-month comparison uses daily averages (total / working days) rather than raw totals, since comparing a partial current month to a full previous month would be misleading.

### Data Flow

- All chart data is passed from controller via `compact()` and consumed in Blade via `@json()` for ApexCharts initialization.
- The donut chart receives percentage values (not raw counts) to match the original CSS pie chart behavior.
- The 30-day trend fills in zero values for days with no attendance records to maintain a continuous line.

### Frontend Choices

- **ApexCharts via CDN** (`@push('scripts')`) — no npm/build step needed. Loaded after page content.
- **Replaced CSS conic-gradient pie chart** with ApexCharts donut — more interactive (hover tooltips, center label).
- **Area chart** (not line) for 30-day trend — gradient fill makes the data more visually distinct.
- **Dark mode support**: Charts detect `dark` class on `<html>` and adjust text/grid colors accordingly.

### Pastel Theme Colors Used

| Name     | Hex       | Usage                        |
|----------|-----------|------------------------------|
| sage     | #C8D5B9   | Hadir (line, donut)          |
| peach    | #F5D5CB   | Terlambat (line, donut)      |
| rose     | #F0D4D8   | Alpha (line, donut)          |
| lavender | #D4C5E2   | Izin/Sakit/Dinas (donut)     |
| sky      | #B8D4E3   | Department bar chart         |

### Gotchas

- `Izin` model has both `status` (legacy: pending/approved/rejected) and `approval_status` (new multi-level approval). The pending request widget uses `status='pending'` for backward compat, while the new Pending Actions widget uses `approval_status='pending'`.
- `anomaly_score` is cast to integer in the Attendance model, so `> 30` comparison works directly in Eloquent.
- `anomaly_flags` is cast to array — template checks `is_array()` before `implode()` for safety.
- `Department::active()` scope filters `is_active = true`; `withCount('employees')` uses the `employees` relationship (hasMany User).
- `WarningLetter.type` holds the SP level (SP1/SP2/SP3); we count all types for the summary.
