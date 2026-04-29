<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Department;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemSetting;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role') && $request->role !== 'Semua') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('jabatan') && $request->jabatan !== 'Semua') {
            $query->where('jabatan', $request->jabatan);
        }

        if ($request->filled('status_karyawan') && $request->status_karyawan !== 'Semua') {
            $query->where('status_karyawan', $request->status_karyawan);
        }

        if ($request->filled('department_id') && $request->department_id !== 'Semua') {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

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
        $departments = Department::active()->orderBy('name')->get();

        return view('admin.employees.index', compact('employees', 'settings', 'departments'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        $shifts = WorkShift::active()->orderBy('name')->get();
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.employees.create', compact('departments', 'shifts', 'companies'));
    }

    public function store(StoreEmployeeRequest $request)
    {
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
            'shift_id' => $request->shift_id,
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'foto' => $fotoPath,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'no_rekening' => $request->no_rekening,
            'nama_bank' => $request->nama_bank,
            'npwp' => $request->npwp,
            'status_pernikahan' => $request->status_pernikahan,
            'jumlah_tanggungan' => $request->jumlah_tanggungan ?? 0,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => $request->tanggal_keluar,
            'status_karyawan' => $request->status_karyawan ?? 'tetap',
            'gaji_pokok' => $request->gaji_pokok ?? 0,
            'no_bpjs_kesehatan' => $request->no_bpjs_kesehatan,
            'no_bpjs_ketenagakerjaan' => $request->no_bpjs_ketenagakerjaan,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        $user->assignRole($request->role);

        $flaskUrl = config('services.flask.url', env('FLASK_INTERNAL_URL', env('FLASK_SERVICE_URL', 'http://face-service:5000')));
        $savedPaths = [];
        $pendingRequest = Http::asMultipart();
        $hasFaces = false;

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

    public function edit(User $employee)
    {
        $departments = Department::active()->orderBy('name')->get();
        $shifts = WorkShift::active()->orderBy('name')->get();
        $companies = Company::active()->orderBy('name')->get();
        $branches = $employee->company_id
            ? CompanyBranch::where('company_id', $employee->company_id)->active()->orderBy('name')->get()
            : collect();

        return view('admin.employees.edit', compact('employee', 'departments', 'shifts', 'companies', 'branches'));
    }

    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'department_id' => $request->department_id,
            'shift_id' => $request->shift_id,
            'company_id' => $request->company_id,
            'branch_id' => $request->branch_id,
            'nik' => $request->nik,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'no_rekening' => $request->no_rekening,
            'nama_bank' => $request->nama_bank,
            'npwp' => $request->npwp,
            'status_pernikahan' => $request->status_pernikahan,
            'jumlah_tanggungan' => $request->jumlah_tanggungan ?? 0,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => $request->tanggal_keluar,
            'status_karyawan' => $request->status_karyawan ?? 'tetap',
            'gaji_pokok' => $request->gaji_pokok ?? 0,
            'no_bpjs_kesehatan' => $request->no_bpjs_kesehatan,
            'no_bpjs_ketenagakerjaan' => $request->no_bpjs_ketenagakerjaan,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
        ]);

        $employee->syncRoles([$request->role]);

        if ($request->filled('password')) {
            $employee->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
