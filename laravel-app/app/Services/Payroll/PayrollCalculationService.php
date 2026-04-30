<?php

namespace App\Services\Payroll;

use App\Exceptions\FormulaException;
use App\Models\FormulaAuditLog;
use App\Models\PayrollDetail;
use App\Models\PayrollDetailItem;
use App\Models\PayrollPeriod;
use App\Models\SalaryComponent;
use App\Models\User;
use App\Services\BpjsCalculator;
use App\Services\Payroll\Formula\FormulaEvaluator;
use App\Services\PayrollService;
use App\Services\Pph21Calculator;
use App\Services\ViolationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollCalculationService
{
    public function __construct(
        private ComponentResolver $componentResolver,
        private ContextBuilder $contextBuilder,
        private TaxRateResolver $taxRateResolver,
        private BpjsRateResolver $bpjsRateResolver,
        private SnapshotService $snapshotService,
        private BpjsCalculator $bpjsCalculator,
        private Pph21Calculator $pph21Calculator,
        private ViolationService $violationService,
        private FormulaEvaluator $formulaEvaluator,
    ) {}

    public function calculateForEmployee(User $user, PayrollPeriod $period): PayrollDetail
    {
        $companyId = $user->company_id ?? 0;
        $periodDate = Carbon::parse($period->end_date);

        $hasDynamicComponents = SalaryComponent::active()
            ->forCompany($companyId)
            ->where(function ($q) {
                $q->where('value_type', 'formula')
                    ->orWhereNotNull('category');
            })
            ->where('category', '!=', 'earning')
            ->exists();

        if (!$hasDynamicComponents && !SalaryComponent::where('value_type', 'formula')->exists()) {
            return app(PayrollService::class)->calculateForEmployee($period, $user);
        }

        $context = $this->contextBuilder->build($user, $period);
        $components = $this->componentResolver->resolve($companyId, $periodDate, $user->id);

        $gajiPokok = (float) $user->gaji_pokok;
        $prorateFactor = $context['prorate_factor'];
        $proratedGaji = $gajiPokok * $prorateFactor;

        $earningItems = [];
        $deductionItems = [];
        $bpjsCompanyItems = [];
        $bpjsEmployeeItems = [];
        $taxItems = [];

        $context['basic_salary'] = $gajiPokok;
        $context['gross_salary'] = $proratedGaji;
        $context['bpjs_basis_salary'] = $proratedGaji;

        $detailItems = [];

        foreach ($components as $component) {
            $amount = $this->resolveComponentValue($component, $context);
            $preProrate = $amount;

            if ($component->is_prorated && $prorateFactor < 1.0) {
                $amount = round($amount * $prorateFactor, 2);
            }

            $amount = $this->clampValue($amount, $component);

            $context[$component->code ?? 'comp_' . $component->id] = $amount;

            $itemData = [
                'component_name' => $component->name,
                'component_type' => $this->mapCategoryToType($component->category ?? $component->type),
                'amount' => $amount,
                'salary_component_id' => $component->id,
                'component_code' => $component->code,
                'component_category' => $component->category ?? $component->type,
                'value_type' => $component->value_type,
                'formula_used' => $component->value_type === 'formula' ? $component->formula : null,
                'formula_variables' => $component->value_type === 'formula' ? $this->extractUsedVariables($component->formula, $context) : null,
                'execution_order' => $component->execution_order,
                'is_prorated' => $component->is_prorated,
                'prorate_factor' => $component->is_prorated ? $prorateFactor : null,
                'pre_prorate_amount' => $component->is_prorated ? $preProrate : null,
            ];

            $type = $itemData['component_type'];
            match ($type) {
                'earning' => $earningItems[] = $itemData,
                'deduction' => $deductionItems[] = $itemData,
                'bpjs_company' => $bpjsCompanyItems[] = $itemData,
                'bpjs_employee' => $bpjsEmployeeItems[] = $itemData,
                'tax' => $taxItems[] = $itemData,
                default => $earningItems[] = $itemData,
            };

            $detailItems[] = $itemData;

            if ($type === 'earning') {
                $context['gross_salary'] += $amount;
            }
        }

        $totalEarnings = array_sum(array_column($earningItems, 'amount'));

        if (empty($earningItems) || $totalEarnings <= 0) {
            $totalEarnings = $proratedGaji;
        }

        $violationDeduction = $this->violationService->getPayrollDeduction($user, $period->period_month);
        if ($violationDeduction > 0) {
            $deductionItems[] = [
                'component_name' => 'Potongan Pelanggaran',
                'component_type' => 'deduction',
                'amount' => $violationDeduction,
                'salary_component_id' => null,
                'component_code' => 'POT_PELANGGARAN',
                'component_category' => 'deduction',
                'value_type' => 'flat',
                'formula_used' => null,
                'formula_variables' => null,
                'execution_order' => null,
                'is_prorated' => false,
                'prorate_factor' => null,
                'pre_prorate_amount' => null,
            ];
        }

        $totalDeductions = array_sum(array_column($deductionItems, 'amount'));

        $bpjs = $this->bpjsCalculator->calculate($proratedGaji, $context['gross_salary']);

        if (empty($bpjsCompanyItems)) {
            $bpjsCompanyItems = [
                $this->makeBpjsItem('BPJS Kesehatan Perusahaan', 'bpjs_company', $bpjs['kes_company']),
                $this->makeBpjsItem('BPJS JHT Perusahaan', 'bpjs_company', $bpjs['jht_company']),
                $this->makeBpjsItem('BPJS JKK', 'bpjs_company', $bpjs['jkk']),
                $this->makeBpjsItem('BPJS JKM', 'bpjs_company', $bpjs['jkm']),
                $this->makeBpjsItem('BPJS JP Perusahaan', 'bpjs_company', $bpjs['jp_company']),
            ];
        }

        if (empty($bpjsEmployeeItems)) {
            $bpjsEmployeeItems = [
                $this->makeBpjsItem('BPJS Kesehatan Karyawan', 'bpjs_employee', $bpjs['kes_employee']),
                $this->makeBpjsItem('BPJS JHT Karyawan', 'bpjs_employee', $bpjs['jht_employee']),
                $this->makeBpjsItem('BPJS JP Karyawan', 'bpjs_employee', $bpjs['jp_employee']),
            ];
        }

        $totalBpjsCompany = array_sum(array_column($bpjsCompanyItems, 'amount'));
        $totalBpjsEmployee = array_sum(array_column($bpjsEmployeeItems, 'amount'));

        $isDecember = str_ends_with($period->period_month, '-12');
        $pph21Amount = 0.0;

        if (empty($taxItems)) {
            if ($isDecember) {
                $year = (int) substr($period->period_month, 0, 4);
                $pph21Amount = $this->pph21Calculator->calculateDecemberCorrection($user, $year);
            } else {
                $terCategory = $user->kategori_ter;
                if ($terCategory) {
                    $pph21Amount = $this->pph21Calculator->calculateMonthlyTER($totalEarnings, $terCategory);
                }
            }

            if ($pph21Amount > 0) {
                $taxItems[] = [
                    'component_name' => 'PPh 21',
                    'component_type' => 'tax',
                    'amount' => $pph21Amount,
                    'salary_component_id' => null,
                    'component_code' => 'PPH21',
                    'component_category' => 'tax',
                    'value_type' => 'flat',
                    'formula_used' => null,
                    'formula_variables' => null,
                    'execution_order' => null,
                    'is_prorated' => false,
                    'prorate_factor' => null,
                    'pre_prorate_amount' => null,
                ];
            }
        } else {
            $pph21Amount = array_sum(array_column($taxItems, 'amount'));
        }

        $netSalary = $totalEarnings - $totalDeductions - $totalBpjsEmployee - $pph21Amount;

        return DB::transaction(function () use (
            $period, $user, $context, $gajiPokok,
            $totalEarnings, $totalDeductions, $totalBpjsCompany, $totalBpjsEmployee,
            $pph21Amount, $netSalary, $prorateFactor,
            $earningItems, $deductionItems, $bpjsCompanyItems, $bpjsEmployeeItems, $taxItems
        ) {
            PayrollDetail::where('payroll_period_id', $period->id)
                ->where('user_id', $user->id)
                ->delete();

            $detail = PayrollDetail::create([
                'payroll_period_id' => $period->id,
                'user_id' => $user->id,
                'working_days' => $context['working_days'],
                'present_days' => $context['present_days'],
                'late_count' => $context['late_count'],
                'late_minutes_total' => $context['_late_minutes_total'] ?? 0,
                'overtime_hours' => $context['overtime_hours'],
                'gaji_pokok' => $gajiPokok,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'total_bpjs_company' => $totalBpjsCompany,
                'total_bpjs_employee' => $totalBpjsEmployee,
                'pph21_amount' => $pph21Amount,
                'net_salary' => $netSalary,
                'prorate_factor' => $prorateFactor,
                'prorate_reason' => null,
                'status' => 'calculated',
            ]);

            $allItems = array_merge($earningItems, $deductionItems, $bpjsCompanyItems, $bpjsEmployeeItems, $taxItems);

            foreach ($allItems as $item) {
                PayrollDetailItem::create(array_merge(
                    ['payroll_detail_id' => $detail->id],
                    $item
                ));
            }

            return $detail;
        });
    }

    public function calculateAll(PayrollPeriod $period): void
    {
        $period->update(['status' => 'processing']);

        $employees = User::where(function ($q) use ($period) {
            $q->whereNull('tanggal_keluar')
                ->orWhere('tanggal_keluar', '>=', $period->start_date);
        })
            ->whereNotNull('status_karyawan')
            ->get();

        $calculatedCount = 0;
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;

        foreach ($employees as $employee) {
            if (!$employee->isPayrollReady()) {
                Log::info("PayrollV2: skipping {$employee->name} (ID:{$employee->id}) — not payroll-ready");
                continue;
            }

            try {
                DB::beginTransaction();
                $detail = $this->calculateForEmployee($employee, $period);
                DB::commit();

                $calculatedCount++;
                $totalGross += (float) $detail->total_earnings;
                $totalDeductions += (float) $detail->total_deductions
                    + (float) $detail->total_bpjs_employee
                    + (float) $detail->pph21_amount;
                $totalNet += (float) $detail->net_salary;
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("PayrollV2: failed for {$employee->name} (ID:{$employee->id}): {$e->getMessage()}");
            }
        }

        $period->update([
            'status' => 'calculated',
            'total_employees' => $calculatedCount,
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
            'calculated_at' => now(),
        ]);
    }

    public function approvePeriod(PayrollPeriod $period, User $approver): void
    {
        $period->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $period->payrollDetails()->update(['status' => 'approved']);
    }

    public function markAsPaid(PayrollPeriod $period): void
    {
        $this->snapshotService->captureFullSnapshot($period);

        $period->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $period->payrollDetails()->update(['status' => 'paid']);
    }

    private function resolveComponentValue(SalaryComponent $component, array $context): float
    {
        if ($component->value_type === 'formula' && $component->formula) {
            try {
                return $this->formulaEvaluator->evaluate($component->formula, $context);
            } catch (FormulaException $e) {
                Log::warning("Formula evaluation failed for {$component->code}: {$e->getMessage()}");

                FormulaAuditLog::create([
                    'user_id' => auth()->id() ?? 0,
                    'action' => 'evaluate_error',
                    'entity_type' => 'salary_component',
                    'entity_id' => $component->id,
                    'old_formula' => $component->formula,
                    'new_formula' => null,
                    'ip_address' => request()->ip(),
                    'metadata' => ['error' => $e->getMessage(), 'context_keys' => array_keys($context)],
                    'created_at' => now(),
                ]);

                return (float) ($component->default_amount ?? 0);
            }
        }

        return (float) ($component->default_amount ?? 0);
    }

    private function clampValue(float $amount, SalaryComponent $component): float
    {
        if ($component->min_value !== null && $amount < (float) $component->min_value) {
            $amount = (float) $component->min_value;
        }
        if ($component->max_value !== null && $amount > (float) $component->max_value) {
            $amount = (float) $component->max_value;
        }
        return round($amount, 2);
    }

    private function mapCategoryToType(?string $category): string
    {
        return match ($category) {
            'basic', 'earning', 'benefit' => 'earning',
            'deduction' => 'deduction',
            'bpjs_company' => 'bpjs_company',
            'bpjs_employee' => 'bpjs_employee',
            'tax' => 'tax',
            default => 'earning',
        };
    }

    private function makeBpjsItem(string $name, string $type, float $amount): array
    {
        return [
            'component_name' => $name,
            'component_type' => $type,
            'amount' => $amount,
            'salary_component_id' => null,
            'component_code' => null,
            'component_category' => $type,
            'value_type' => 'flat',
            'formula_used' => null,
            'formula_variables' => null,
            'execution_order' => null,
            'is_prorated' => false,
            'prorate_factor' => null,
            'pre_prorate_amount' => null,
        ];
    }

    private function extractUsedVariables(?string $formula, array $context): ?array
    {
        if (!$formula) {
            return null;
        }

        $result = $this->formulaEvaluator->validate($formula);
        if (!$result['valid']) {
            return null;
        }

        $used = [];
        foreach ($result['variables_used'] as $var) {
            if (array_key_exists($var, $context)) {
                $used[$var] = $context[$var];
            }
        }

        return $used ?: null;
    }
}
