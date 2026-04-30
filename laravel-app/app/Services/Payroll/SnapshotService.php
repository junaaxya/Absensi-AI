<?php

namespace App\Services\Payroll;

use App\Models\BpjsRateVersion;
use App\Models\PayrollPeriod;
use App\Models\PayrollSnapshot;
use App\Models\SalaryComponent;
use App\Models\SystemSetting;
use App\Models\TaxPtkpRateVersion;
use App\Models\TaxTerRateVersion;

class SnapshotService
{
    public function captureFullSnapshot(PayrollPeriod $period): void
    {
        $date = $period->end_date;

        $this->captureSnapshot($period, 'bpjs_rates', $this->collectBpjsRates($date));
        $this->captureSnapshot($period, 'ter_rates', $this->collectTerRates($date));
        $this->captureSnapshot($period, 'ptkp_rates', $this->collectPtkpRates($date));
        $this->captureSnapshot($period, 'salary_components', $this->collectSalaryComponents());
        $this->captureSnapshot($period, 'company_config', $this->collectCompanyConfig());
    }

    public function verifySnapshot(PayrollPeriod $period): array
    {
        $diffs = [];
        $date = $period->end_date;

        $snapshotMap = [
            'bpjs_rates' => fn () => $this->collectBpjsRates($date),
            'ter_rates' => fn () => $this->collectTerRates($date),
            'ptkp_rates' => fn () => $this->collectPtkpRates($date),
            'salary_components' => fn () => $this->collectSalaryComponents(),
            'company_config' => fn () => $this->collectCompanyConfig(),
        ];

        foreach ($snapshotMap as $type => $collector) {
            $existing = PayrollSnapshot::where('payroll_period_id', $period->id)
                ->where('snapshot_type', $type)
                ->first();

            if (!$existing) {
                $diffs[$type] = ['status' => 'missing'];
                continue;
            }

            $currentData = $collector();
            $currentHash = hash('sha256', json_encode($currentData));

            if ($currentHash !== $existing->snapshot_hash) {
                $diffs[$type] = [
                    'status' => 'changed',
                    'snapshot_hash' => $existing->snapshot_hash,
                    'current_hash' => $currentHash,
                ];
            }
        }

        return $diffs;
    }

    private function captureSnapshot(PayrollPeriod $period, string $type, array $data): void
    {
        $json = json_encode($data);
        $hash = hash('sha256', $json);

        PayrollSnapshot::updateOrCreate(
            [
                'payroll_period_id' => $period->id,
                'snapshot_type' => $type,
            ],
            [
                'snapshot_data' => $data,
                'snapshot_hash' => $hash,
                'created_at' => now(),
            ]
        );
    }

    private function collectBpjsRates($date): array
    {
        return BpjsRateVersion::where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date);
            })
            ->get()
            ->toArray();
    }

    private function collectTerRates($date): array
    {
        return TaxTerRateVersion::where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date);
            })
            ->get()
            ->toArray();
    }

    private function collectPtkpRates($date): array
    {
        return TaxPtkpRateVersion::where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date);
            })
            ->get()
            ->toArray();
    }

    private function collectSalaryComponents(): array
    {
        return SalaryComponent::active()
            ->orderBy('execution_order')
            ->get()
            ->toArray();
    }

    private function collectCompanyConfig(): array
    {
        $settings = SystemSetting::first();
        if (!$settings) {
            return [];
        }

        return [
            'weekend_days' => $settings->weekend_days ?? [],
            'work_start_time' => $settings->work_start_time ?? null,
            'late_tolerance_minutes' => $settings->late_tolerance_minutes ?? null,
            'overtime_start_time' => $settings->overtime_start_time ?? null,
            'overtime_end_time' => $settings->overtime_end_time ?? null,
            'jkk_risk_group' => $settings->jkk_risk_group ?? 1,
            'bpjs_kes_ceiling' => $settings->bpjs_kes_ceiling ?? 12000000,
            'bpjs_jp_ceiling' => $settings->bpjs_jp_ceiling ?? 10042300,
        ];
    }
}
