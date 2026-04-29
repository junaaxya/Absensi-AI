<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Izin;
use App\Models\Violation;
use App\Models\WarningLetter;
use App\Models\PayrollPeriod;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $authUser = auth()->user();

        // 1. Total Karyawan
        $userQuery = User::query();
        \App\Services\RoleBasedScope::scopeUsers($userQuery, $authUser);
        $totalKaryawan = $userQuery->count();

        // 2. Kehadiran Hari Ini
        $attendanceQuery = Attendance::whereDate('tanggal', $today);
        \App\Services\RoleBasedScope::scopeAttendance($attendanceQuery, $authUser);
        $attendances = $attendanceQuery->get();

        $hadirTepatWaktu = $attendances->where('status', 'tepat_waktu')->count();
        $hadirTerlambat  = $attendances->where('status', 'terlambat')->count();
        $totalHadir      = $hadirTepatWaktu + $hadirTerlambat;

        // 3. Izin / Sakit / Cuti / Dinas
        $izinQuery = Izin::whereDate('tanggal_mulai', '<=', $today)
                     ->whereDate('tanggal_selesai', '>=', $today)
                     ->where('status', 'approved');
        \App\Services\RoleBasedScope::scopeIzin($izinQuery, $authUser);
        $izins = $izinQuery->get();

        $sakit = $izins->where('jenis', 'sakit')->count();
        $izin  = $izins->where('jenis', 'izin')->count();
        $cuti  = $izins->where('jenis', 'cuti')->count();
        $dinas = $izins->where('jenis', 'dinas')->count();
        $totalIzin = $sakit + $izin + $cuti + $dinas;

        // 4. Alpha
        $alpha = max(0, $totalKaryawan - ($totalHadir + $totalIzin));

        // 5. Pending Request (legacy)
        $pendingQuery = Izin::where('status', 'pending');
        \App\Services\RoleBasedScope::scopeIzin($pendingQuery, $authUser);
        $pendingRequest = $pendingQuery->count();

        // 6. Persentase Kehadiran
        $persentaseHadir = $totalKaryawan > 0 ? round(($totalHadir / $totalKaryawan) * 100) : 0;
        $persentaseTerlambat = $totalKaryawan > 0 ? round(($hadirTerlambat / $totalKaryawan) * 100) : 0;
        $persentaseIzin = $totalKaryawan > 0 ? round(($totalIzin / $totalKaryawan) * 100) : 0;
        $persentaseAlpha = $totalKaryawan > 0 ? round(($alpha / $totalKaryawan) * 100) : 0;

        // ── Analytics: Trend indicators vs last month ──
        $trendData = $this->calculateTrendIndicators($authUser, $today);

        // ── Analytics: 30-day attendance trend ──
        $attendanceTrend = $this->getAttendanceTrend($authUser, $today);

        // ── Analytics: Department comparison ──
        $departmentComparison = $this->getDepartmentComparison($today);

        // ── Analytics: Top late employees this month ──
        $topLateEmployees = $this->getTopLateEmployees($authUser, $today);

        // ── Analytics: Violation summary this month ──
        $violationSummary = $this->getViolationSummary($today);

        // ── Analytics: Pending approvals ──
        $pendingApprovals = $this->getPendingApprovals($authUser);

        // ── Analytics: Recent anomalies ──
        $recentAnomalies = $this->getRecentAnomalies($authUser);

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'totalHadir',
            'hadirTepatWaktu',
            'hadirTerlambat',
            'sakit',
            'izin',
            'cuti',
            'dinas',
            'alpha',
            'pendingRequest',
            'persentaseHadir',
            'persentaseTerlambat',
            'persentaseIzin',
            'persentaseAlpha',
            'today',
            'trendData',
            'attendanceTrend',
            'departmentComparison',
            'topLateEmployees',
            'violationSummary',
            'pendingApprovals',
            'recentAnomalies'
        ));
    }

    /**
     * Calculate trend indicators comparing this month vs last month.
     */
    private function calculateTrendIndicators($authUser, Carbon $today): array
    {
        $thisMonthStart = $today->copy()->startOfMonth();
        $lastMonthStart = $today->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $today->copy()->subMonth()->endOfMonth();

        $thisMonthDays = max(1, $today->diffInWeekdays($thisMonthStart) + 1);
        $lastMonthDays = max(1, $lastMonthEnd->diffInWeekdays($lastMonthStart) + 1);

        $thisMonthQuery = Attendance::whereBetween('tanggal', [$thisMonthStart, $today]);
        \App\Services\RoleBasedScope::scopeAttendance($thisMonthQuery, $authUser);
        $thisMonthAttendances = $thisMonthQuery->get();

        $thisHadir = $thisMonthAttendances->whereIn('status', ['tepat_waktu', 'terlambat'])->count();
        $thisTerlambat = $thisMonthAttendances->where('status', 'terlambat')->count();

        $lastMonthQuery = Attendance::whereBetween('tanggal', [$lastMonthStart, $lastMonthEnd]);
        \App\Services\RoleBasedScope::scopeAttendance($lastMonthQuery, $authUser);
        $lastMonthAttendances = $lastMonthQuery->get();

        $lastHadir = $lastMonthAttendances->whereIn('status', ['tepat_waktu', 'terlambat'])->count();
        $lastTerlambat = $lastMonthAttendances->where('status', 'terlambat')->count();

        // Normalize to daily averages for fair month-over-month comparison
        $thisHadirAvg = $thisHadir / $thisMonthDays;
        $lastHadirAvg = $lastHadir / $lastMonthDays;
        $thisTerlambatAvg = $thisTerlambat / $thisMonthDays;
        $lastTerlambatAvg = $lastTerlambat / $lastMonthDays;

        $thisIzinCount = Izin::whereBetween('tanggal_mulai', [$thisMonthStart, $today])
            ->where('status', 'approved')->count();
        $lastIzinCount = Izin::whereBetween('tanggal_mulai', [$lastMonthStart, $lastMonthEnd])
            ->where('status', 'approved')->count();
        $thisIzinAvg = $thisIzinCount / $thisMonthDays;
        $lastIzinAvg = $lastIzinCount / $lastMonthDays;

        return [
            'hadir' => $this->computeTrendPercent($thisHadirAvg, $lastHadirAvg),
            'terlambat' => $this->computeTrendPercent($thisTerlambatAvg, $lastTerlambatAvg),
            'izin' => $this->computeTrendPercent($thisIzinAvg, $lastIzinAvg),
        ];
    }

    /**
     * Compute percentage change between two values.
     */
    private function computeTrendPercent(float $current, float $previous): array
    {
        if ($previous == 0) {
            $percent = $current > 0 ? 100 : 0;
        } else {
            $percent = round((($current - $previous) / $previous) * 100);
        }

        return [
            'percent' => abs($percent),
            'direction' => $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat'),
        ];
    }

    /**
     * Get daily attendance counts for the last 30 days (hadir, terlambat, alpha).
     */
    private function getAttendanceTrend($authUser, Carbon $today): array
    {
        $startDate = $today->copy()->subDays(29);

        $query = Attendance::select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw("SUM(CASE WHEN status = 'tepat_waktu' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as terlambat")
            )
            ->whereBetween('tanggal', [$startDate, $today])
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('date');

        \App\Services\RoleBasedScope::scopeAttendance($query, $authUser);

        $results = $query->get()->keyBy('date');

        $userCountQuery = User::query();
        \App\Services\RoleBasedScope::scopeUsers($userCountQuery, $authUser);
        $totalEmployees = $userCountQuery->count();

        $dates = [];
        $hadirData = [];
        $terlambatData = [];
        $alphaData = [];

        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('d M');

            $dayData = $results->get($dateStr);
            $hadir = $dayData ? (int) $dayData->hadir : 0;
            $terlambat = $dayData ? (int) $dayData->terlambat : 0;

            $hadirData[] = $hadir;
            $terlambatData[] = $terlambat;
            $alphaData[] = max(0, $totalEmployees - $hadir - $terlambat);
        }

        return [
            'dates' => $dates,
            'hadir' => $hadirData,
            'terlambat' => $terlambatData,
            'alpha' => $alphaData,
        ];
    }

    /**
     * Get attendance rate per department for bar chart.
     */
    private function getDepartmentComparison(Carbon $today): array
    {
        $departments = Department::active()
            ->withCount('employees')
            ->having('employees_count', '>', 0)
            ->get();

        $labels = [];
        $rates = [];

        foreach ($departments as $dept) {
            $attendanceCount = Attendance::whereDate('tanggal', $today)
                ->whereIn('status', ['tepat_waktu', 'terlambat'])
                ->whereHas('user', function ($q) use ($dept) {
                    $q->where('department_id', $dept->id);
                })
                ->count();

            $rate = $dept->employees_count > 0
                ? round(($attendanceCount / $dept->employees_count) * 100)
                : 0;

            $labels[] = $dept->name;
            $rates[] = $rate;
        }

        return [
            'labels' => $labels,
            'rates' => $rates,
        ];
    }

    /**
     * Get top 10 employees with most late arrivals this month.
     */
    private function getTopLateEmployees($authUser, Carbon $today): array
    {
        $monthStart = $today->copy()->startOfMonth();

        $query = Attendance::select('user_id', DB::raw('COUNT(*) as late_count'))
            ->where('status', 'terlambat')
            ->whereBetween('tanggal', [$monthStart, $today])
            ->groupBy('user_id')
            ->orderByDesc('late_count')
            ->limit(10);

        \App\Services\RoleBasedScope::scopeAttendance($query, $authUser);

        $results = $query->get();

        $employees = [];
        foreach ($results as $row) {
            $user = User::with('department')->find($row->user_id);
            if ($user) {
                $employees[] = [
                    'name' => $user->name,
                    'department' => $user->department->name ?? '-',
                    'late_count' => $row->late_count,
                ];
            }
        }

        return $employees;
    }

    /**
     * Get violation summary for this month.
     */
    private function getViolationSummary(Carbon $today): array
    {
        $monthStart = $today->copy()->startOfMonth();

        $totalViolations = Violation::whereBetween('tanggal', [$monthStart, $today])->count();
        $totalPoints = Violation::whereBetween('tanggal', [$monthStart, $today])->sum('points');
        $spCount = WarningLetter::whereBetween('issued_at', [$monthStart, $today])->count();

        return [
            'total_violations' => $totalViolations,
            'total_points' => (int) $totalPoints,
            'sp_count' => $spCount,
        ];
    }

    /**
     * Get pending approvals count (izin + payroll).
     */
    private function getPendingApprovals($authUser): array
    {
        $pendingIzinQuery = Izin::where('approval_status', 'pending');
        \App\Services\RoleBasedScope::scopeIzin($pendingIzinQuery, $authUser);
        $pendingIzin = $pendingIzinQuery->count();

        $pendingPayroll = PayrollPeriod::where('status', 'calculated')->count();

        return [
            'izin' => $pendingIzin,
            'payroll' => $pendingPayroll,
        ];
    }

    /**
     * Get last 5 anomaly-flagged attendances (anomaly_score > 30).
     */
    private function getRecentAnomalies($authUser): \Illuminate\Support\Collection
    {
        $query = Attendance::with('user')
            ->where('anomaly_score', '>', 30)
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->limit(5);

        \App\Services\RoleBasedScope::scopeAttendance($query, $authUser);

        return $query->get();
    }
}
