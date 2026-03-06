<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Izin;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // 1. Total Karyawan
        $userQuery = User::query();
        \App\Services\RoleBasedScope::scopeUsers($userQuery, auth()->user());
        $totalKaryawan = $userQuery->count();

        // 2. Kehadiran Hari Ini
        $attendanceQuery = Attendance::whereDate('tanggal', $today);
        \App\Services\RoleBasedScope::scopeAttendance($attendanceQuery, auth()->user());
        $attendances = $attendanceQuery->get();
        
        $hadirTepatWaktu = $attendances->where('status', 'tepat_waktu')->count();
        $hadirTerlambat  = $attendances->where('status', 'terlambat')->count();
        $totalHadir      = $hadirTepatWaktu + $hadirTerlambat;

        // 3. Izin / Sakit / Cuti / Dinas
        $izinQuery = Izin::whereDate('tanggal_mulai', '<=', $today)
                     ->whereDate('tanggal_selesai', '>=', $today)
                     ->where('status', 'approved');
        \App\Services\RoleBasedScope::scopeIzin($izinQuery, auth()->user());
        $izins = $izinQuery->get();

        $sakit = $izins->where('jenis', 'sakit')->count();
        $izin  = $izins->where('jenis', 'izin')->count();
        $cuti  = $izins->where('jenis', 'cuti')->count();
        $dinas = $izins->where('jenis', 'dinas')->count();
        $totalIzin = $sakit + $izin + $cuti + $dinas;

        // 4. Alpha (Belum absen & tidak izin)
        // Logic sederhana: Total Karyawan - (Hadir + Izin)
        // Note: Ini bisa minus jika ada admin yg absen atau data tidak sinkron, jadi max(0, ...)
        $alpha = max(0, $totalKaryawan - ($totalHadir + $totalIzin));

        // 5. Pending Request
        $pendingQuery = Izin::where('status', 'pending');
        \App\Services\RoleBasedScope::scopeIzin($pendingQuery, auth()->user());
        $pendingRequest = $pendingQuery->count();

        // 6. Persentase Kehadiran
        $persentaseHadir = $totalKaryawan > 0 ? round(($totalHadir / $totalKaryawan) * 100) : 0;
        $persentaseTerlambat = $totalKaryawan > 0 ? round(($hadirTerlambat / $totalKaryawan) * 100) : 0;
        $persentaseIzin = $totalKaryawan > 0 ? round(($totalIzin / $totalKaryawan) * 100) : 0;
        $persentaseAlpha = $totalKaryawan > 0 ? round(($alpha / $totalKaryawan) * 100) : 0;

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
            'today'
        ));
    }
}
