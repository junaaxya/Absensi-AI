<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesSettingsTabs;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class AdminLeaveTypeController extends Controller
{
    use AuthorizesSettingsTabs;

    public function store(Request $request)
    {
        $this->authorizeTabAccess($request, 'tipe_cuti');

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:leave_types,code',
            'days_quota' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
        ]);

        LeaveType::create([
            'name' => $request->name,
            'code' => $request->code,
            'days_quota' => $request->days_quota ?? 12,
            'is_paid' => $request->boolean('is_paid', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Jenis cuti berhasil ditambahkan.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $this->authorizeTabAccess($request, 'tipe_cuti');

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:leave_types,code,' . $leaveType->id,
            'days_quota' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $leaveType->update([
            'name' => $request->name,
            'code' => $request->code,
            'days_quota' => $request->days_quota ?? 12,
            'is_paid' => $request->boolean('is_paid', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Jenis cuti berhasil diperbarui.');
    }

    public function destroy(Request $request, LeaveType $leaveType)
    {
        $this->authorizeTabAccess($request, 'tipe_cuti');

        $leaveType->delete();

        return back()->with('success', 'Jenis cuti berhasil dihapus.');
    }
}
