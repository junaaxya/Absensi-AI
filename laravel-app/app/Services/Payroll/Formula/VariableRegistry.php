<?php

namespace App\Services\Payroll\Formula;

class VariableRegistry
{
    public static function definitions(): array
    {
        return [
            'working_days' => [
                'source' => 'attendance',
                'description' => 'Jumlah hari kerja dalam periode',
            ],
            'present_days' => [
                'source' => 'attendance',
                'description' => 'Jumlah hari hadir karyawan',
            ],
            'absent_days' => [
                'source' => 'attendance',
                'description' => 'Jumlah hari tidak hadir',
            ],
            'late_count' => [
                'source' => 'attendance',
                'description' => 'Jumlah keterlambatan',
            ],
            'overtime_hours' => [
                'source' => 'attendance',
                'description' => 'Total jam lembur',
            ],
            'leave_days_unpaid' => [
                'source' => 'leave',
                'description' => 'Jumlah hari cuti tidak berbayar',
            ],
            'sick_days' => [
                'source' => 'leave',
                'description' => 'Jumlah hari sakit',
            ],
            'basic_salary' => [
                'source' => 'employee',
                'description' => 'Gaji pokok karyawan',
            ],
            'gross_salary' => [
                'source' => 'calculated',
                'description' => 'Total pendapatan kotor (akumulasi)',
            ],
            'prorate_factor' => [
                'source' => 'calculated',
                'description' => 'Faktor prorata (0-1)',
            ],
            'bpjs_basis_salary' => [
                'source' => 'calculated',
                'description' => 'Dasar gaji untuk perhitungan BPJS',
            ],
            'taxable_income' => [
                'source' => 'calculated',
                'description' => 'Penghasilan kena pajak',
            ],
            'ter_rate' => [
                'source' => 'tax',
                'description' => 'Tarif efektif rata-rata PPh 21',
            ],
            'ptkp_status' => [
                'source' => 'employee',
                'description' => 'Status PTKP (numerik: 1=TK/0, dst)',
            ],
            'ptkp_amount' => [
                'source' => 'tax',
                'description' => 'Jumlah PTKP tahunan',
            ],
        ];
    }

    public static function availableNames(): array
    {
        return array_keys(static::definitions());
    }
}
