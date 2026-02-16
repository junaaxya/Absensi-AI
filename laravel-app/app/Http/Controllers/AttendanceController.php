<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
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

        // Filter status bulan/tahun (default: bulan berjalan)
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // absensi hari ini (untuk panel atas - TETAP HARI INI)
        $attendanceToday = Attendance::where('user_id', $user->id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        // riwayat absensi user (difilter bulan/tahun)
        $attendanceHistory = Attendance::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'attendanceToday' => $attendanceToday,
            'attendanceHistory' => $attendanceHistory,
            'bulan' => $bulan,
            'tahun' => $tahun,
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
