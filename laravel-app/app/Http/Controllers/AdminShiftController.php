<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesSettingsTabs;
use App\Models\WorkShift;
use Illuminate\Http\Request;

class AdminShiftController extends Controller
{
    use AuthorizesSettingsTabs;

    public function store(Request $request)
    {
        $this->authorizeTabAccess($request, 'shift_kerja');

        $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'days' => 'nullable|array',
            'days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        WorkShift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'days' => $request->days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'is_active' => $request->boolean('is_active', true),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Shift kerja berhasil ditambahkan.');
    }

    public function update(Request $request, WorkShift $shift)
    {
        $this->authorizeTabAccess($request, 'shift_kerja');

        $request->validate([
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'days' => 'nullable|array',
            'days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $shift->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'days' => $request->days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'is_active' => $request->boolean('is_active', true),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Shift kerja berhasil diperbarui.');
    }

    public function destroy(Request $request, WorkShift $shift)
    {
        $this->authorizeTabAccess($request, 'shift_kerja');

        if ($shift->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus shift yang masih memiliki karyawan. Pindahkan karyawan terlebih dahulu.');
        }

        $shift->delete();

        return back()->with('success', 'Shift kerja berhasil dihapus.');
    }
}
