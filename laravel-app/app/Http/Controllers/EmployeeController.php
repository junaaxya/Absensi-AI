<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\SystemSetting;

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
            $query->whereHas('roles', function($q) use ($request) { $q->where('name', $request->role); });
        }

        // 2. Filter Jabatan
        if ($request->filled('jabatan') && $request->jabatan !== 'Semua') {
            $query->where('jabatan', $request->jabatan);
        }

        // 3. Search (Name or NIP/Username)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // 4. Face data status filter
        if ($request->filled('face_status')) {
            if ($request->face_status === 'registered') {
                $query->where('has_face_data', true);
            }

            if ($request->face_status === 'unregistered') {
                $query->where('has_face_data', false);
            }
        }

        $employees = $query->latest()
            ->paginate(10)
            ->withQueryString();
        $settings = SystemSetting::first();

        return view('admin.employees.index', compact('employees', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        return view('admin.employees.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'face_photos.*' => ['nullable', 'image', 'max:2048'],
            'base64_faces' => ['nullable', 'array'],
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'department_id' => $request->department_id,
            'foto' => $fotoPath,
        ]);

        $user->assignRole($request->role);

        $flaskUrl = config('services.flask.url', env('FLASK_INTERNAL_URL', env('FLASK_SERVICE_URL', 'http://face-service:5000')));
        $savedPaths = [];
        $pendingRequest = Http::asMultipart();
        $hasFaces = false;

        // Handle Base64 from Camera
        if ($request->has('base64_faces') && is_array($request->base64_faces)) {
            foreach ($request->base64_faces as $index => $base64) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                    $data = substr($base64, strpos($base64, ',') + 1);
                    $data = base64_decode($data);
                    $filename = "{$user->username}_" . time() . "_{$index}.jpg";
                    Storage::disk('local')->put("temp_faces/{$filename}", $data);
                    $savedPaths[] = "temp_faces/{$filename}";
                    $absolutePath = Storage::disk('local')->path("temp_faces/{$filename}");
                    $pendingRequest->attach('photos', fopen($absolutePath, 'r'), $filename);
                    $hasFaces = true;
                }
            }
        }

        // Handle File Uploads
        if ($request->hasFile('face_photos')) {
            foreach ($request->file('face_photos') as $index => $photo) {
                $filename = "{$user->username}_" . time() . "_file_{$index}." . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('temp_faces', $filename, 'local');
                $savedPaths[] = $path;
                $absolutePath = Storage::disk('local')->path($path);
                $pendingRequest->attach('photos', fopen($absolutePath, 'r'), $filename);
                $hasFaces = true;
            }
        }

        // Send to Flask
        if ($hasFaces) {
            try {
                $response = $pendingRequest->post("{$flaskUrl}/register-face", [
                    'username' => $user->username,
                ]);

                if ($response->successful()) {
                    $user->update(['has_face_data' => true]);
                } else {
                    Log::error("Flask Error: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Face Registration Error: " . $e->getMessage());
            } finally {
                foreach ($savedPaths as $path) {
                    Storage::delete($path);
                }
            }
        }

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee)
    {
        $departments = Department::active()->orderBy('name')->get();
        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employee)
    {

        $request->validate([

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->id],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $employee->id],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'department_id' => $request->department_id,
        ]);
        $employee->syncRoles([$request->role]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $employee->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->assignRole($request->role);
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee)
    {
        $employee->delete();
        $user->assignRole($request->role);
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
