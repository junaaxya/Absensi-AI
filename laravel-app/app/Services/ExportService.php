<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportAttendance(string $startDate, string $endDate, ?int $deptId = null): StreamedResponse
    {
        $query = Attendance::with(['user', 'user.department'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->orderBy('jam_masuk');

        if ($deptId) {
            $query->whereHas('user', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        $attendances = $query->get();

        return new StreamedResponse(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Tanggal',
                'Nama',
                'Departemen',
                'Jabatan',
                'Jam Masuk',
                'Jam Keluar',
                'Status',
                'Kegiatan',
                'Skor Similaritas Masuk',
                'Skor Similaritas Keluar',
            ]);

            foreach ($attendances as $att) {
                fputcsv($handle, [
                    $att->tanggal->format('Y-m-d'),
                    $att->user->name ?? '-',
                    $att->user->department->name ?? '-',
                    $att->user->jabatan ?? '-',
                    $att->jam_masuk ? Carbon::parse($att->jam_masuk)->format('H:i:s') : '-',
                    $att->jam_keluar ? Carbon::parse($att->jam_keluar)->format('H:i:s') : '-',
                    $att->status ?? '-',
                    $att->kegiatan ?? '-',
                    $att->similarity_score_in ?? '-',
                    $att->similarity_score_out ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="attendance-' . $startDate . '-to-' . $endDate . '.csv"',
        ]);
    }

    public function exportEmployees(?int $deptId = null, ?string $status = null): StreamedResponse
    {
        $query = User::with(['department', 'shift'])
            ->orderBy('name');

        if ($deptId) {
            $query->where('department_id', $deptId);
        }

        if ($status === 'active') {
            $query->where('has_face_data', true);
        } elseif ($status === 'inactive') {
            $query->where('has_face_data', false);
        }

        $employees = $query->get();

        return new StreamedResponse(function () use ($employees) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Nama',
                'Email',
                'Username',
                'Jabatan',
                'Departemen',
                'Shift',
                'Role',
                'Data Wajah Terdaftar',
            ]);

            foreach ($employees as $emp) {
                fputcsv($handle, [
                    $emp->name,
                    $emp->email,
                    $emp->username ?? '-',
                    $emp->jabatan ?? '-',
                    $emp->department->name ?? '-',
                    $emp->shift->name ?? '-',
                    $emp->role ?? 'user',
                    $emp->has_face_data ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="employees-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
