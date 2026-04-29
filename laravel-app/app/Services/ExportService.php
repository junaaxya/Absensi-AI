<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\SystemSetting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

        if ($status && in_array($status, ['tetap', 'kontrak', 'magang'])) {
            $query->where('status_karyawan', $status);
        } elseif ($status === 'active') {
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
                'NIK',
                'Status Karyawan',
                'Tanggal Masuk',
                'No. Telepon',
                'Jenis Kelamin',
                'Gaji Pokok',
                'Status Pernikahan',
                'Jumlah Tanggungan',
                'BPJS Kesehatan',
                'BPJS Ketenagakerjaan',
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
                    $emp->getRoleNames()->first() ?? '-',
                    $emp->nik ?? '-',
                    $emp->status_karyawan ? ucfirst($emp->status_karyawan) : '-',
                    $emp->tanggal_masuk ? $emp->tanggal_masuk->format('Y-m-d') : '-',
                    $emp->no_telepon ?? '-',
                    $emp->jenis_kelamin === 'L' ? 'Laki-laki' : ($emp->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
                    $emp->gaji_pokok ? number_format($emp->gaji_pokok, 0, ',', '.') : '0',
                    $emp->status_pernikahan === 'TK' ? 'Tidak Kawin' : ($emp->status_pernikahan === 'K' ? 'Kawin' : '-'),
                    $emp->jumlah_tanggungan ?? 0,
                    $emp->no_bpjs_kesehatan ?? '-',
                    $emp->no_bpjs_ketenagakerjaan ?? '-',
                    $emp->has_face_data ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="employees-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export attendance report as PDF.
     *
     * @param  array{date_from?: string, date_to?: string, department_id?: int|null, user_id?: int|null}  $filters
     */
    public function exportAttendancePdf(array $filters): string
    {
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->endOfMonth()->toDateString();
        $departmentId = $filters['department_id'] ?? null;
        $userId = $filters['user_id'] ?? null;

        $query = Attendance::with(['user', 'user.department'])
            ->whereBetween('tanggal', [$dateFrom, $dateTo])
            ->orderBy('tanggal')
            ->orderBy('jam_masuk');

        if ($departmentId) {
            $query->whereHas('user', fn ($q) => $q->where('department_id', $departmentId));
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->get();
        $summary = $this->buildAttendanceSummary($attendances);

        $settings = SystemSetting::first();
        $department = $departmentId ? Department::find($departmentId) : null;

        $pdf = Pdf::loadView('pdf.attendance-report', [
            'attendances' => $attendances,
            'summary' => $summary,
            'settings' => $settings,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'department' => $department,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = 'laporan-absensi-' . $dateFrom . '-' . $dateTo . '.pdf';
        $path = storage_path('app/exports/' . $filename);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }

    /**
     * Build per-employee attendance summary statistics.
     *
     * @return Collection<int, array{name: string, nik: string|null, department: string, hadir: int, terlambat: int, alpha: int, izin: int, total_jam: float}>
     */
    private function buildAttendanceSummary(Collection $attendances): Collection
    {
        return $attendances->groupBy('user_id')->map(function (Collection $records) {
            $user = $records->first()->user;
            $hadir = $records->filter(fn ($a) => $a->jam_masuk !== null)->count();
            $terlambat = $records->filter(fn ($a) => $a->status === 'terlambat')->count();
            $alpha = $records->filter(fn ($a) => $a->status === 'alpha')->count();
            $izin = $records->filter(fn ($a) => in_array($a->status, ['izin', 'sakit', 'cuti']))->count();

            $totalJam = $records->reduce(function (float $carry, $att) {
                if ($att->jam_masuk && $att->jam_keluar) {
                    $masuk = Carbon::parse($att->jam_masuk);
                    $keluar = Carbon::parse($att->jam_keluar);
                    $carry += $masuk->diffInMinutes($keluar) / 60;
                }
                return $carry;
            }, 0.0);

            return [
                'name' => $user->name ?? '-',
                'nik' => $user->nik ?? null,
                'department' => $user->department->name ?? '-',
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'alpha' => $alpha,
                'izin' => $izin,
                'total_jam' => round($totalJam, 1),
            ];
        })->values();
    }
}
