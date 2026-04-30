<?php

namespace App\Services\Payroll;

use App\Models\TaxPtkpRate;
use App\Models\TaxPtkpRateVersion;
use App\Models\TaxTerRate;
use App\Models\TaxTerRateVersion;
use DateTimeInterface;

class TaxRateResolver
{
    public function getTerRate(string $category, float $grossIncome, DateTimeInterface $date): float
    {
        if ($grossIncome <= 0) {
            return 0.0;
        }

        $rate = TaxTerRateVersion::where('category', $category)
            ->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date);
            })
            ->where('min_income', '<=', $grossIncome)
            ->where('max_income', '>=', $grossIncome)
            ->value('rate');

        if ($rate !== null) {
            return (float) $rate;
        }

        $fallbackRate = TaxTerRate::where('category', $category)
            ->where('min_income', '<=', $grossIncome)
            ->where('max_income', '>=', $grossIncome)
            ->value('rate');

        return $fallbackRate !== null ? (float) $fallbackRate : 0.0;
    }

    public function getPtkpAmount(string $status, DateTimeInterface $date): float
    {
        $amount = TaxPtkpRateVersion::where('status', $status)
            ->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date);
            })
            ->value('amount');

        if ($amount !== null) {
            return (float) $amount;
        }

        $fallbackAmount = TaxPtkpRate::where('status', $status)->value('amount');

        return $fallbackAmount !== null ? (float) $fallbackAmount : 54000000.0;
    }
}
