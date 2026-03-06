<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\User;
use Carbon\Carbon;

class AdminAbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Izin::with('user');
        \App\Services\RoleBasedScope::scopeIzin($query, auth()->user());

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $statusMap = [
                'Menunggu' => 'pending',
                'Di Validasi' => 'approved',
                'Di Tolak' => 'rejected'
            ];
            $status = $statusMap[$request->status] ?? strtolower($request->status);
            $query->where('status', $status);
        }

        // Date Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('tanggal_mulai', '<=', $request->end_date)
                  ->whereDate('tanggal_selesai', '>=', $request->start_date);
        }

        $izins = $query->latest()->paginate(10)->withQueryString();

        return view('admin.absence', compact('izins'));
    }

    public function updateStatus(Request $request, Izin $izin)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $izin->update(['status' => $request->status]);

        $message = $request->status === 'approved' ? 'Pengajuan disetujui.' : 'Pengajuan ditolak.';
        
        return back()->with('success', $message);
    }
}
