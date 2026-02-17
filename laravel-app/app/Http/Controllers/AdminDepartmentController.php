<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesSettingsTabs;
use App\Models\Department;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    use AuthorizesSettingsTabs;

    public function store(Request $request)
    {
        $this->authorizeTabAccess($request, 'departemen');

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:departments,code',
            'description' => 'nullable|string',
            'head_user_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        Department::create($request->only([
            'name', 'code', 'description', 'head_user_id', 'parent_id', 'is_active',
        ]));

        return back()->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Department $department)
    {
        $this->authorizeTabAccess($request, 'departemen');

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'head_user_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        if ($request->parent_id == $department->id) {
            return back()->with('error', 'Departemen tidak bisa menjadi parent dari dirinya sendiri.');
        }

        $department->update($request->only([
            'name', 'code', 'description', 'head_user_id', 'parent_id', 'is_active',
        ]));

        return back()->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Request $request, Department $department)
    {
        $this->authorizeTabAccess($request, 'departemen');

        if ($department->employees()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus departemen yang masih memiliki karyawan. Pindahkan karyawan terlebih dahulu.');
        }

        $department->delete();

        return back()->with('success', 'Departemen berhasil dihapus.');
    }
}
