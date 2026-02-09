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
    public function index()
    {
        $employees = User::where('role', '!=', 'admin') // Opsional: sembunyikan admin utama jika perlu
            ->latest()
            ->paginate(10);

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
            // 'nip' => ['nullable', 'string'], // Jika ada kolom NIP
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
         // Validasi update bisa disesuaikan (misal password nullable)
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
