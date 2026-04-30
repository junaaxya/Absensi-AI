<?php

namespace App\Services\Payroll;

use App\Models\BpjsRateVersion;
use DateTimeInterface;

class BpjsRateResolver
{
    private const FALLBACK_RATES = [
        'jht' => ['employer_rate' => 0.037, 'employee_rate' => 0.02, 'max_salary_basis' => null],
        'jkk' => ['employer_rate' => 0.0024, 'employee_rate' => 0.0, 'max_salary_basis' => null],
        'jkm' => ['employer_rate' => 0.003, 'employee_rate' => 0.0, 'max_salary_basis' => null],
        'jp' => ['employer_rate' => 0.02, 'employee_rate' => 0.01, 'max_salary_basis' => 10042300],
        'bpjs_kesehatan' => ['employer_rate' => 0.04, 'employee_rate' => 0.01, 'max_salary_basis' => 12000000],
    ];

    public function getRates(DateTimeInterface $date, ?int $companyId = null): array
    {
        $programs = ['jht', 'jkk', 'jkm', 'jp', 'bpjs_kesehatan'];
        $rates = [];

        foreach ($programs as $program) {
            $version = BpjsRateVersion::where('program', $program)
                ->where('effective_from', '<=', $date)
                ->where(function ($q) use ($date) {
                    $q->whereNull('effective_until')
                        ->orWhere('effective_until', '>=', $date);
                })
                ->where(function ($q) use ($companyId) {
                    $q->whereNull('company_id');
                    if ($companyId) {
                        $q->orWhere('company_id', $companyId);
                    }
                })
                ->orderByRaw('company_id IS NULL ASC')
                ->first();

            if ($version) {
                $rates[$program] = [
                    'employer_rate' => (float) $version->employer_rate / 100,
                    'employee_rate' => (float) $version->employee_rate / 100,
                    'max_salary_basis' => $version->max_salary_basis ? (float) $version->max_salary_basis : null,
                    'min_salary_basis' => $version->min_salary_basis ? (float) $version->min_salary_basis : null,
                ];
            } else {
                $fallback = self::FALLBACK_RATES[$program];
                $rates[$program] = [
                    'employer_rate' => $fallback['employer_rate'],
                    'employee_rate' => $fallback['employee_rate'],
                    'max_salary_basis' => $fallback['max_salary_basis'],
                    'min_salary_basis' => null,
                ];
            }
        }

        return $rates;
    }
}
