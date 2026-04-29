<?php

namespace App\Services;

use App\Models\SystemSetting;

class BpjsCalculator
{
    private const JKK_RATES = [
        1 => 0.0024,
        2 => 0.0054,
        3 => 0.0089,
        4 => 0.0127,
        5 => 0.0174,
    ];

    public function calculate(float $gajiPokok, float $totalFixedEarnings, array $options = []): array
    {
        $settings = SystemSetting::first();

        $jkkRiskGroup = $options['jkk_risk_group'] ?? $settings->jkk_risk_group ?? 1;
        $kesCeiling = $settings->bpjs_kes_ceiling ?? 12000000;
        $jpCeiling = $settings->bpjs_jp_ceiling ?? 10042300;

        $kesBase = min($totalFixedEarnings, $kesCeiling);
        $kesCompany = round($kesBase * 0.04);
        $kesEmployee = round($kesBase * 0.01);

        $jhtCompany = round($gajiPokok * 0.037);
        $jhtEmployee = round($gajiPokok * 0.02);

        $jkkRate = self::JKK_RATES[$jkkRiskGroup] ?? self::JKK_RATES[1];
        $jkk = round($gajiPokok * $jkkRate);

        $jkm = round($gajiPokok * 0.003);

        $jpBase = min($gajiPokok, $jpCeiling);
        $jpCompany = round($jpBase * 0.02);
        $jpEmployee = round($jpBase * 0.01);

        $totalCompany = $kesCompany + $jhtCompany + $jkk + $jkm + $jpCompany;
        $totalEmployee = $kesEmployee + $jhtEmployee + $jpEmployee;

        return [
            'kes_company'    => $kesCompany,
            'kes_employee'   => $kesEmployee,
            'jht_company'    => $jhtCompany,
            'jht_employee'   => $jhtEmployee,
            'jkk'            => $jkk,
            'jkm'            => $jkm,
            'jp_company'     => $jpCompany,
            'jp_employee'    => $jpEmployee,
            'total_company'  => $totalCompany,
            'total_employee' => $totalEmployee,
        ];
    }
}
