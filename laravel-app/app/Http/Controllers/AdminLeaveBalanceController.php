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
        $tab = $request->input('tab', 'saldo');
        $leaveTypes = LeaveType::orderBy('code')->get();

        $query = User::where('is_approved', true)
            ->where(function ($q) {
                $q->whereNull('tanggal_keluar')
                  ->orWhere('tanggal_keluar', '>', now());
            });

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

        $hasBalances = LeaveBalance::where('year', $year)->exists();

        return view('admin.leave-balances.index', compact(
            'employees', 'leaveTypes', 'balances', 'year', 'tab', 'hasBalances'
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

    public function storeLeaveType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:leave_types,code',
            'days_quota' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
        ]);

        LeaveType::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'days_quota' => $request->days_quota,
            'is_paid' => $request->boolean('is_paid', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.leave-balances.index', ['tab' => 'tipe'])
            ->with('success', "Tipe cuti '{$request->name}' berhasil ditambahkan.");
    }

    public function updateLeaveType(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:leave_types,code,' . $leaveType->id,
            'days_quota' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $leaveType->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'days_quota' => $request->days_quota,
            'is_paid' => $request->boolean('is_paid'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.leave-balances.index', ['tab' => 'tipe'])
            ->with('success', "Tipe cuti '{$request->name}' berhasil diperbarui.");
    }

    public function destroyLeaveType(LeaveType $leaveType)
    {
        $name = $leaveType->name;

        $usedCount = LeaveBalance::where('leave_type_id', $leaveType->id)->count();
        if ($usedCount > 0) {
            return back()->with('error', "Tidak bisa menghapus '{$name}' karena sudah digunakan oleh {$usedCount} saldo karyawan.");
        }

        $leaveType->delete();

        return redirect()->route('admin.leave-balances.index', ['tab' => 'tipe'])
            ->with('success', "Tipe cuti '{$name}' berhasil dihapus.");
    }
}
