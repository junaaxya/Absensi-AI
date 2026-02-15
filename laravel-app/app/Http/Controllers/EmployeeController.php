<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // 1. Filter Role: Default hide main admin if not explicitly filtering
        // Logic: if role filter is set, use it. If not, hide 'admin' unless we want to see other admins.
        // Generally good to hide current user or super admin, but let's keep it simple based on request.
        // Let's filter out current user to avoid self-delete issues, or just basic role filter.
        
        if ($request->filled('role') && $request->role !== 'Semua') {
            $query->where('role', strtolower($request->role));
        }

        // 2. Filter Jabatan
        if ($request->filled('jabatan') && $request->jabatan !== 'Semua') {
            $query->where('jabatan', $request->jabatan);
        }

        // 3. Search (Name or NIP/Username)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:admin,karyawan'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'role' => $request->role,
        ]);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employee)
    {
         $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$employee->id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$employee->id],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:admin,karyawan'],
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $employee->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
