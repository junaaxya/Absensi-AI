<?php

namespace App\Services\Payroll;

use App\Models\EmployeeSalaryComponent;
use App\Models\SalaryComponent;
use App\Models\SalaryComponentRule;
use DateTimeInterface;
use Illuminate\Support\Collection;

class ComponentResolver
{
    public function resolve(int $companyId, DateTimeInterface $date, ?int $userId = null): Collection
    {
        $components = SalaryComponent::active()
            ->forCompany($companyId)
            ->effectiveOn($date)
            ->orderBy('execution_order')
            ->get();

        $rules = SalaryComponentRule::where('company_id', $companyId)
            ->where('is_active', true)
            ->where(function ($q) use ($date) {
                $q->where(function ($inner) use ($date) {
                    $inner->whereNull('effective_from')
                        ->orWhere('effective_from', '<=', $date);
                })->where(function ($inner) use ($date) {
                    $inner->whereNull('effective_until')
                        ->orWhere('effective_until', '>=', $date);
                });
            })
            ->get()
            ->keyBy('salary_component_id');

        $employeeOverrides = collect();
        if ($userId) {
            $employeeOverrides = EmployeeSalaryComponent::where('user_id', $userId)
                ->active()
                ->effectiveOn($date)
                ->get()
                ->keyBy('salary_component_id');
        }

        return $components->map(function (SalaryComponent $component) use ($rules, $employeeOverrides) {
            $resolved = clone $component;

            if ($rules->has($component->id)) {
                $rule = $rules->get($component->id);
                if ($rule->override_formula !== null) {
                    $resolved->value_type = 'formula';
                    $resolved->formula = $rule->override_formula;
                } elseif ($rule->override_amount !== null) {
                    $resolved->value_type = 'flat';
                    $resolved->default_amount = $rule->override_amount;
                }
                if ($rule->min_value !== null) {
                    $resolved->min_value = $rule->min_value;
                }
                if ($rule->max_value !== null) {
                    $resolved->max_value = $rule->max_value;
                }
                if ($rule->execution_order !== null) {
                    $resolved->execution_order = $rule->execution_order;
                }
            }

            if ($employeeOverrides->has($component->id)) {
                $override = $employeeOverrides->get($component->id);
                if ($override->value_type === 'formula' && $override->formula) {
                    $resolved->value_type = 'formula';
                    $resolved->formula = $override->formula;
                } else {
                    $resolved->value_type = 'flat';
                    $resolved->default_amount = $override->amount;
                }
            }

            return $resolved;
        })->sortBy('execution_order')->values();
    }
}
