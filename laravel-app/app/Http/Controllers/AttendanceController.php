<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\SystemSetting;
use App\Models\Announcement;
use App\Models\Izin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Dashboard karyawan
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = trim((string) $request->get('q', ''));

        // absensi hari ini (untuk panel atas - TETAP HARI INI)
        $attendanceToday = Attendance::where('user_id', $user->id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        $historyQuery = Attendance::where('user_id', $user->id);

        if ($startDate && $endDate) {
            $historyQuery->whereBetween('tanggal', [$startDate, $endDate]);
        } else {
            $historyQuery->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }

        if ($search !== '') {
            $historyQuery->where(function ($query) use ($search) {
                $query->where('status', 'like', "%{$search}%")
                    ->orWhere('kegiatan', 'like', "%{$search}%")
                    ->orWhere('jam_masuk', 'like', "%{$search}%")
                    ->orWhere('jam_keluar', 'like', "%{$search}%");
            });
        }

        $attendanceHistory = $historyQuery
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_masuk')
            ->paginate(10)
            ->withQueryString();

        $settings = SystemSetting::first();
        $officeName = $settings?->office_name ?? 'Kantor Pusat';

        $userWithShift = $user->load('shift');
        if ($userWithShift->shift && $userWithShift->shift->is_active) {
            $workStartTime = $userWithShift->shift->start_time;
            $workEndTime = $userWithShift->shift->end_time;
        } else {
            $workStartTime = $settings?->work_start_time ?? '08:00:00';
            $workEndTime = $settings?->work_end_time ?? '17:00:00';
        }

        $activeAnnouncements = Announcement::active()
            ->whereIn('target_role', ['all', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $hadir_count = Attendance::where('user_id', auth()->id())
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->whereNotNull('jam_masuk')
            ->count();

        $late_count = Attendance::where('user_id', auth()->id())
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status', 'terlambat')
            ->count();

        $izin_count = Izin::where('user_id', auth()->id())
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'approved')
            ->count();

        return view('dashboard', [
            'user' => $user,
            'attendanceToday' => $attendanceToday,
            'attendanceHistory' => $attendanceHistory,
            'hadir_count' => $hadir_count,
            'late_count' => $late_count,
            'izin_count' => $izin_count,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'search' => $search,
            'officeName' => $officeName,
            'officeGpsTolerance' => $settings?->office_gps_tolerance ?? 150,
            'workStartTime' => $workStartTime,
            'workEndTime' => $workEndTime,
            'activeAnnouncements' => $activeAnnouncements,
        ]);

    }



    /**
     * Laporan Absensi untuk Admin
     */
    public function adminIndex(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $userId = $request->get('user_id');

        $query = Attendance::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->latest('tanggal')
            ->latest('jam_masuk')
            ->paginate(20)
            ->withQueryString();

        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.attendance.index', compact('attendances', 'users', 'startDate', 'endDate', 'userId'));
    }
}
