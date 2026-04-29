<?php

namespace App\Services;

use App\Models\PayrollDetail;
use App\Models\PayrollDetailItem;
use App\Models\SystemSetting;
use App\Models\TaxPtkpRate;
use App\Models\TaxTerRate;
use App\Models\User;

class Pph21Calculator
{
    public function calculateMonthlyTER(float $brutoMonthly, string $terCategory): float
    {
        if ($brutoMonthly <= 0) {
            return 0;
        }

        $rate = TaxTerRate::where('category', $terCategory)
            ->where('min_income', '<=', $brutoMonthly)
            ->where('max_income', '>=', $brutoMonthly)
            ->value('rate');

        if ($rate === null) {
            return 0;
        }

        return round($brutoMonthly * (float) $rate);
    }

    public function calculateDecemberCorrection(User $user, int $year): float
    {
        $details = PayrollDetail::where('user_id', $user->id)
            ->whereHas('payrollPeriod', function ($q) use ($year) {
                $q->where('period_month', 'like', $year . '-%');
            })
            ->with(['items', 'payrollPeriod'])
            ->get();

        $annualBruto = $details->sum('total_earnings');

        // Biaya jabatan: 5% dari bruto, maks Rp 6.000.000/tahun
        $biayaJabatan = min($annualBruto * 0.05, 6000000);

        // Sum JHT + JP employee contributions for the year from detail items
        $annualBpjsJhtJpEmployee = 0;
        foreach ($details as $detail) {
            $annualBpjsJhtJpEmployee += $detail->items
                ->where('component_type', 'bpjs_employee')
                ->whereIn('component_name', ['BPJS JHT Karyawan', 'BPJS JP Karyawan'])
                ->sum('amount');
        }

        $ptkpStatus = $user->status_ptkp;
        $ptkpAmount = TaxPtkpRate::where('status', $ptkpStatus)->value('amount') ?? 54000000;

        $pkp = $annualBruto - $biayaJabatan - $annualBpjsJhtJpEmployee - $ptkpAmount;

        if ($pkp <= 0) {
            return 0;
        }

        $annualPph21 = $this->calculateProgressiveTax($pkp);

        $settings = SystemSetting::first();
        if (($settings->no_npwp_surcharge_enabled ?? true) && empty($user->getRawOriginal('npwp'))) {
            $annualPph21 *= 1.20;
        }

        $pph21JanNov = $details
            ->filter(fn ($d) => !str_ends_with($d->payrollPeriod->period_month, '-12'))
            ->sum('pph21_amount');

        return round(max($annualPph21 - $pph21JanNov, 0));
    }

    private function calculateProgressiveTax(float $pkp): float
    {
        $brackets = [
            [60000000,    0.05],
            [190000000,   0.15],  // 60jt - 250jt
            [250000000,   0.25],  // 250jt - 500jt
            [4500000000,  0.30],  // 500jt - 5M
            [PHP_FLOAT_MAX, 0.35],
        ];

        $tax = 0;
        $remaining = $pkp;

        foreach ($brackets as $bracket) {
            if ($remaining <= 0) {
                break;
            }

            $taxable = min($remaining, $bracket[0]);
            $tax += $taxable * $bracket[1];
            $remaining -= $taxable;
        }

        return round($tax);
    }
}
