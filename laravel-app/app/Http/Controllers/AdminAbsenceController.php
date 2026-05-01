<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Services\LeaveService;
use App\View\Composers\AdminSidebarComposer;

class AdminAbsenceController extends Controller
{
    public function __construct(
        private LeaveService $leaveService
    ) {}

    public function index(Request $request)
    {
        AdminSidebarComposer::markSeen('izin');

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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('tanggal_mulai', '<=', $request->end_date)
                  ->whereDate('tanggal_selesai', '>=', $request->start_date);
        }

        $izins = $query->latest()->paginate(10)->withQueryString();

        $totalPending = Izin::where('status', 'pending')->count();
        $totalApproved = Izin::where('status', 'approved')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $totalRejected = Izin::where('status', 'rejected')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        return view('admin.absence', compact('izins', 'totalPending', 'totalApproved', 'totalRejected'));
    }

    public function approve(Request $request, Izin $izin)
    {
        $user = auth()->user();

        if ($izin->current_approver_id && $izin->current_approver_id !== $user->id) {
            if (!$user->hasRole(['Direktur', 'Vice President'])) {
                return back()->with('error', 'Anda bukan approver yang ditunjuk untuk pengajuan ini.');
            }
        }

        $this->leaveService->approve($izin, $user);

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, Izin $izin)
    {
        $user = auth()->user();

        if ($izin->current_approver_id && $izin->current_approver_id !== $user->id) {
            if (!$user->hasRole(['Direktur', 'Vice President'])) {
                return back()->with('error', 'Anda bukan approver yang ditunjuk untuk pengajuan ini.');
            }
        }

        $reason = $request->input('rejection_reason', '');
        $this->leaveService->reject($izin, $user, $reason);

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function updateStatus(Request $request, Izin $izin)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($request->status === 'approved') {
            $this->leaveService->approve($izin, auth()->user());
        } else {
            $this->leaveService->reject($izin, auth()->user(), '');
        }

        $message = $request->status === 'approved' ? 'Pengajuan disetujui.' : 'Pengajuan ditolak.';

        return back()->with('success', $message);
    }
}
