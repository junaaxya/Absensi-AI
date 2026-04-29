<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Izin;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Services\LeaveService;

class IzinController extends Controller
{
    public function __construct(
        private LeaveService $leaveService
    ) {}

    public function index()
    {
        $riwayat = Izin::where('user_id', Auth::id())
            ->with(['leaveType', 'currentApprover'])
            ->latest()
            ->paginate(10);

        $leaveTypes = LeaveType::active()->get();

        $balances = LeaveBalance::where('user_id', Auth::id())
            ->where('year', now()->year)
            ->with('leaveType')
            ->get();

        return view('izin.index', compact('riwayat', 'leaveTypes', 'balances'));
    }

    public function store(Request $request)
    {
        $rules = [
            'jenis' => 'required|string|in:izin,sakit,cuti,dinas,wfa',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'dokumen' => 'nullable|file|mimes:pdf,docx,doc,jpg,jpeg,png|max:5120',
            'leave_type_id' => 'nullable|exists:leave_types,id',
        ];

        if ($request->jenis === 'wfa') {
            $rules['wfa_location'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $path = null;
        if ($request->hasFile('dokumen')) {
            $path = $request->file('dokumen')->store('izin', 'public');
        }

        $izin = new Izin([
            'user_id' => Auth::id(),
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'dokumen' => $path,
            'status' => 'pending',
            'leave_type_id' => $request->leave_type_id,
            'approval_status' => 'pending',
            'wfa_location' => $request->wfa_location,
        ]);

        $izin->save();

        $firstApprover = $this->leaveService->getNextApprover($izin);
        if ($firstApprover) {
            $izin->update(['current_approver_id' => $firstApprover->id]);
        }

        return back()->with('success', 'Pengajuan ketidakhadiran berhasil dikirim.');
    }

    public function getLeaveBalance(Request $request)
    {
        $request->validate(['leave_type_id' => 'required|exists:leave_types,id']);

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $balance = $this->leaveService->getBalance(Auth::user(), $leaveType, now()->year);

        return response()->json([
            'quota' => $balance->quota,
            'used' => $balance->used,
            'remaining' => $balance->remaining,
            'carry_over' => $balance->carry_over,
        ]);
    }

    public function wfaCheckin(Request $request, Izin $izin)
    {
        if ($izin->user_id !== Auth::id() || $izin->jenis !== 'wfa') {
            abort(403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $checkins = $izin->wfa_daily_checkins ?? [];
        $checkins[] = [
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        $izin->update(['wfa_daily_checkins' => $checkins]);

        return response()->json(['success' => true, 'message' => 'Check-in WFA berhasil.']);
    }
}
