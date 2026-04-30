<?php

namespace App\Services\Payroll;

use App\Models\Company;
use App\Models\CompanyTemplateApplication;
use App\Models\PayrollTemplate;
use App\Models\PayrollTemplateItem;
use App\Models\SalaryComponent;
use Illuminate\Support\Facades\DB;

class PayrollTemplateService
{
    public function apply(Company $company, PayrollTemplate $template, string $mode, array $options = []): CompanyTemplateApplication
    {
        $excludedCodes = $options['excluded_codes'] ?? [];
        $companyId = $company->id;

        return DB::transaction(function () use ($company, $template, $mode, $excludedCodes, $companyId) {
            $snapshot = null;
            $created = 0;
            $skipped = 0;
            $updated = 0;

            $existingComponents = SalaryComponent::where('company_id', $companyId)->get();

            if ($mode === 'replace') {
                $snapshot = $existingComponents->map(fn ($c) => $c->toArray())->toArray();
                SalaryComponent::where('company_id', $companyId)->delete();
            }

            $items = $template->items()
                ->whereNotIn('code', $excludedCodes)
                ->orderBy('execution_order')
                ->get();

            foreach ($items as $item) {
                if ($mode === 'merge') {
                    $exists = SalaryComponent::where('company_id', $companyId)
                        ->where('code', $item->code)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }
                }

                $this->createComponentFromItem($item, $companyId, $template->id);
                $created++;
            }

            $application = CompanyTemplateApplication::create([
                'company_id' => $companyId,
                'payroll_template_id' => $template->id,
                'applied_by' => auth()->id(),
                'applied_at' => now(),
                'mode' => $mode,
                'components_created' => $created,
                'components_skipped' => $skipped,
                'components_updated' => $updated,
                'rollback_snapshot' => $snapshot,
            ]);

            $template->increment('popularity_score');

            return $application;
        });
    }

    public function applyWithoutCompany(PayrollTemplate $template, string $mode, array $options = []): CompanyTemplateApplication
    {
        $excludedCodes = $options['excluded_codes'] ?? [];

        return DB::transaction(function () use ($template, $mode, $excludedCodes) {
            $snapshot = null;
            $created = 0;
            $skipped = 0;
            $updated = 0;

            $existingComponents = SalaryComponent::whereNull('company_id')->get();

            if ($mode === 'replace') {
                $snapshot = $existingComponents->map(fn ($c) => $c->toArray())->toArray();
                SalaryComponent::whereNull('company_id')->delete();
            }

            $items = $template->items()
                ->whereNotIn('code', $excludedCodes)
                ->orderBy('execution_order')
                ->get();

            foreach ($items as $item) {
                if ($mode === 'merge') {
                    $exists = SalaryComponent::whereNull('company_id')
                        ->where('code', $item->code)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }
                }

                $this->createComponentFromItem($item, null, $template->id);
                $created++;
            }

            $application = CompanyTemplateApplication::create([
                'company_id' => null,
                'payroll_template_id' => $template->id,
                'applied_by' => auth()->id(),
                'applied_at' => now(),
                'mode' => $mode,
                'components_created' => $created,
                'components_skipped' => $skipped,
                'components_updated' => $updated,
                'rollback_snapshot' => $snapshot,
            ]);

            $template->increment('popularity_score');

            return $application;
        });
    }

    public function rollback(CompanyTemplateApplication $application): bool
    {
        return DB::transaction(function () use ($application) {
            $companyId = $application->company_id;

            if ($companyId) {
                SalaryComponent::where('company_id', $companyId)
                    ->where('source_template_id', $application->payroll_template_id)
                    ->delete();
            } else {
                SalaryComponent::whereNull('company_id')
                    ->where('source_template_id', $application->payroll_template_id)
                    ->delete();
            }

            if ($application->mode === 'replace' && !empty($application->rollback_snapshot)) {
                foreach ($application->rollback_snapshot as $componentData) {
                    unset($componentData['id'], $componentData['created_at'], $componentData['updated_at']);
                    SalaryComponent::create($componentData);
                }
            }

            $application->delete();

            return true;
        });
    }

    public function preview(Company $company = null, PayrollTemplate $template, string $mode): array
    {
        $companyId = $company?->id;
        $items = $template->items()->orderBy('execution_order')->get();

        $existingCodes = $companyId
            ? SalaryComponent::where('company_id', $companyId)->pluck('code')->toArray()
            : SalaryComponent::whereNull('company_id')->pluck('code')->toArray();

        $willCreate = [];
        $willSkip = [];
        $willReplace = [];

        foreach ($items as $item) {
            $exists = in_array($item->code, $existingCodes);

            if ($mode === 'fresh' || $mode === 'replace') {
                $willCreate[] = $item;
            } elseif ($mode === 'merge') {
                if ($exists) {
                    $willSkip[] = $item;
                } else {
                    $willCreate[] = $item;
                }
            }
        }

        if ($mode === 'replace') {
            $willReplace = SalaryComponent::query()
                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                ->when(!$companyId, fn ($q) => $q->whereNull('company_id'))
                ->get()
                ->toArray();
        }

        return [
            'will_create' => $willCreate,
            'will_skip' => $willSkip,
            'will_replace' => $willReplace,
            'existing_count' => count($existingCodes),
        ];
    }

    public function recommend(Company $company = null): array
    {
        $templates = PayrollTemplate::active()->orderBy('sort_order')->get();

        if (!$company) {
            return $templates->map(fn ($t) => [
                'template' => $t,
                'score' => $t->popularity_score,
                'reason' => null,
            ])->toArray();
        }

        $companySettings = $company->settings ?? [];
        $industryType = $companySettings['industry_type'] ?? null;
        $employeeCount = $company->employees()->count();

        $companySize = match (true) {
            $employeeCount <= 50 => 'small',
            $employeeCount <= 200 => 'medium',
            default => 'large',
        };

        return $templates->map(function ($template) use ($industryType, $companySize) {
            $score = $template->popularity_score;
            $reasons = [];

            if ($industryType && $template->industry_type === $industryType) {
                $score += 100;
                $reasons[] = 'Sesuai industri';
            }

            if ($template->company_size === $companySize) {
                $score += 50;
                $reasons[] = 'Sesuai ukuran perusahaan';
            }

            return [
                'template' => $template,
                'score' => $score,
                'reason' => implode(', ', $reasons) ?: null,
            ];
        })->sortByDesc('score')->values()->toArray();
    }

    private function createComponentFromItem(PayrollTemplateItem $item, ?int $companyId, int $templateId): SalaryComponent
    {
        return SalaryComponent::create([
            'company_id' => $companyId,
            'name' => $item->name,
            'code' => $item->code,
            'type' => $item->type,
            'category' => $item->category,
            'value_type' => $item->value_type,
            'formula' => $item->formula,
            'default_amount' => $item->default_amount,
            'is_taxable' => $item->is_taxable,
            'is_fixed' => $item->is_fixed,
            'is_prorated' => $item->is_prorated,
            'prorate_basis' => $item->prorate_basis ?? 'working_days',
            'execution_order' => $item->execution_order,
            'depends_on' => $item->depends_on,
            'min_value' => $item->min_value,
            'max_value' => $item->max_value,
            'description' => $item->description,
            'help_text' => $item->help_text,
            'group_label' => $item->group_label,
            'is_active' => true,
            'source_template_id' => $templateId,
            'source_template_item_id' => $item->id,
        ]);
    }
}
