<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveService;

class AdminLeaveBalanceController extends Controller
{
    public function __construct(
        private LeaveService $leaveService
    ) {}

    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $leaveTypes = LeaveType::active()->get();

        $query = User::whereNull('tanggal_keluar')
            ->orWhere('tanggal_keluar', '>', now());

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(20)->withQueryString();

        $employeeIds = $employees->pluck('id');
        $balances = LeaveBalance::where('year', $year)
            ->whereIn('user_id', $employeeIds)
            ->get()
            ->groupBy('user_id');

        return view('admin.leave-balances.index', compact(
            'employees',
            'leaveTypes',
            'balances',
            'year'
        ));
    }

    public function update(Request $request, LeaveBalance $leaveBalance)
    {
        $request->validate([
            'remaining' => 'required|integer|min:0',
        ]);

        $diff = $request->remaining - $leaveBalance->remaining;
        $leaveBalance->update([
            'remaining' => $request->remaining,
            'used' => max(0, $leaveBalance->used - $diff),
        ]);

        return back()->with('success', 'Saldo cuti berhasil diperbarui.');
    }

    public function initialize(Request $request)
    {
        $request->validate(['year' => 'required|integer|min:2020|max:2099']);

        $this->leaveService->initializeYearlyBalances($request->year);

        return back()->with('success', "Saldo cuti tahun {$request->year} berhasil diinisialisasi.");
    }
}
