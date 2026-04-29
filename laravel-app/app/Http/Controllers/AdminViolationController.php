<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationType;
use App\Services\ViolationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminViolationController extends Controller
{
    public function index(Request $request)
    {
        $query = Violation::with(['user', 'violationType', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('tanggal', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('tanggal', '<=', $request->date_to);
        }

        if ($request->filled('violation_type')) {
            $query->where('violation_type_id', $request->violation_type);
        }

        $violations = $query->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $violationTypes = ViolationType::where('is_active', true)->get();

        return view('admin.violations.index', compact('violations', 'violationTypes'));
    }

    public function create()
    {
        $users = User::whereNotNull('status_karyawan')
            ->whereNull('tanggal_keluar')
            ->orderBy('name')
            ->get();

        $violationTypes = ViolationType::where('is_active', true)->get();

        return view('admin.violations.create', compact('users', 'violationTypes'));
    }

    public function store(Request $request, ViolationService $violationService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'tanggal' => 'required|date',
            'points' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        Violation::create([
            'user_id' => $request->user_id,
            'violation_type_id' => $request->violation_type_id,
            'tanggal' => $request->tanggal,
            'points' => $request->points,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        $user = User::find($request->user_id);
        $yearMonth = Carbon::parse($request->tanggal)->format('Y-m');
        $violationService->checkWarningThreshold($user, $yearMonth);

        return redirect()->route('admin.violations.index')
            ->with('success', 'Pelanggaran berhasil ditambahkan.');
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $yearMonth = $month;

        $usersWithViolations = User::whereHas('violations', function ($q) use ($yearMonth) {
            $q->where('tanggal', 'like', $yearMonth . '%');
        })
            ->with(['violations' => function ($q) use ($yearMonth) {
                $q->where('tanggal', 'like', $yearMonth . '%')
                    ->with('violationType')
                    ->orderBy('tanggal');
            }, 'warningLetters' => function ($q) use ($yearMonth) {
                $q->where('period_month', $yearMonth);
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($user) use ($yearMonth) {
                $user->monthly_total_points = $user->violations
                    ->where('tanggal', '>=', Carbon::parse($yearMonth . '-01'))
                    ->sum('points');
                $user->monthly_violation_count = $user->violations->count();
                $user->active_sp = $user->warningLetters->sortByDesc('type')->first();
                return $user;
            });

        return view('admin.violations.monthly-report', compact('usersWithViolations', 'month'));
    }
}
