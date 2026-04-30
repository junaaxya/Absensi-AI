<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Attendance;
use App\Services\LeaveService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TeamController extends Controller
{
    public function __construct(
        private LeaveService $leaveService
    ) {}

    public function izinApproval(Request $request)
    {
        $query = Izin::with(['user', 'leaveType', 'currentApprover']);
        \App\Services\RoleBasedScope::scopeIzin($query, auth()->user());

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_status') && $request->filter_status !== '') {
            $query->where('status', $request->filter_status);
        }

        $izins = $query->latest()->paginate(15)->withQueryString();

        $totalPending = Izin::where('status', 'pending')->count();
        $totalApproved = Izin::where('status', 'approved')
            ->whereMonth('updated_at', now()->month)->count();
        $totalRejected = Izin::where('status', 'rejected')
            ->whereMonth('updated_at', now()->month)->count();

        return view('team.izin-approval', compact('izins', 'totalPending', 'totalApproved', 'totalRejected'));
    }

    public function approveIzin(Izin $izin)
    {
        $this->leaveService->approve($izin, auth()->user());

        Cache::forget('admin_sidebar_badges_' . auth()->id());

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function rejectIzin(Request $request, Izin $izin)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $this->leaveService->reject($izin, auth()->user(), $request->rejection_reason);

        Cache::forget('admin_sidebar_badges_' . auth()->id());

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function attendance(Request $request)
    {
        $query = Attendance::with('user');
        \App\Services\RoleBasedScope::scopeAttendance($query, auth()->user());

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } else {
            $query->whereDate('tanggal', Carbon::today());
        }

        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $status = strtolower($request->status);
            if (in_array($status, ['hadir', 'terlambat'])) {
                $query->where('status', $status === 'hadir' ? 'tepat_waktu' : 'terlambat');
            }
        }

        $attendances = $query->latest('tanggal')->paginate(20)->withQueryString();

        return view('team.attendance', compact('attendances'));
    }
}
