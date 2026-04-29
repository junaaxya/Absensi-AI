# Enterprise Upgrade Plan

## Phase 3A: Visit Attendance (Absensi Kunjungan)

- [x] 3A.1: Migration — `visit_attendances` and `visit_locations` tables
- [x] 3A.2: Models (VisitAttendance, VisitLocation) + API Controller + Routes
- [x] 3A.3: User-Facing Visit UI in dashboard.blade.php
- [x] 3A.4: Admin Visit Management (Controller, Views, Routes, Sidebar, Permission)

## Phase 3B: Enhanced Leave & WFA Workflow

- [x] 3B.1: Migration — enhance_leave_system (izins columns + leave_balances table)
- [x] 3B.2: Models (LeaveBalance) + Izin model update + User model update + LeaveService
- [x] 3B.3: Controller updates (IzinController, AdminAbsenceController, AdminLeaveBalanceController) + Routes + Seeder
- [x] 3B.4: View updates (user izin form, admin absence, leave balance management, sidebar)

## Phase 3C: Dashboard Analytics Enhancement

- [x] 3C.1: Enhance AdminDashboardController with analytics data (trend indicators, 30-day trend, department comparison, top late employees, violation summary, pending approvals, anomaly detection)
- [x] 3C.2: Update Admin Dashboard View with ApexCharts (donut, line, bar charts), anomaly panel, pending actions widget, trend indicators on stat cards

## Phase 3D: Enhanced Export & Auto-Checkout

- [x] 3D.1: PDF Export — ExportService.exportAttendancePdf(), PDF Blade template, AdminExportController.exportAttendancePdf(), route + UI buttons (CSV/PDF side-by-side)
- [x] 3D.2: Auto-Checkout Command — attendance:auto-checkout, scheduler registration (dailyAt 23:55), manual trigger button in kebijakan_absensi settings tab
