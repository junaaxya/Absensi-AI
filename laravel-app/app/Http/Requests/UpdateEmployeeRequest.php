<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employeeId],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $employeeId],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'shift_id' => ['nullable', 'exists:work_shifts,id'],
            'foto' => ['nullable', 'image', 'max:2048'],

            'nik' => ['nullable', 'string', 'size:16', 'unique:users,nik,' . $employeeId],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'no_rekening' => ['nullable', 'string', 'max:50'],
            'nama_bank' => ['nullable', 'string', 'max:100'],
            'npwp' => ['nullable', 'string', 'max:30'],
            'status_pernikahan' => ['nullable', 'in:TK,K'],
            'jumlah_tanggungan' => ['integer', 'min:0', 'max:3'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tanggal_keluar' => ['nullable', 'date', 'after:tanggal_masuk'],
            'status_karyawan' => ['in:tetap,kontrak,magang'],
            'gaji_pokok' => ['nullable', 'numeric', 'min:0'],
            'no_bpjs_kesehatan' => ['nullable', 'string', 'max:30'],
            'no_bpjs_ketenagakerjaan' => ['nullable', 'string', 'max:30'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
