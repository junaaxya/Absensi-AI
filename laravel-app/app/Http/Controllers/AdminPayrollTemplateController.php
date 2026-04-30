<?php

namespace App\Http\Controllers;

use App\Models\CompanyTemplateApplication;
use App\Models\PayrollTemplate;
use App\Models\SalaryComponent;
use App\Services\Payroll\PayrollTemplateService;
use Illuminate\Http\Request;

class AdminPayrollTemplateController extends Controller
{
    public function __construct(
        private PayrollTemplateService $templateService
    ) {}

    public function index()
    {
        $templates = PayrollTemplate::active()
            ->withCount('items')
            ->orderBy('sort_order')
            ->get();

        $user = auth()->user();
        $company = $user->company_id ? $user->company : null;
        $recommendations = $this->templateService->recommend($company);

        $recommendedSlugs = collect($recommendations)
            ->filter(fn ($r) => $r['reason'] !== null)
            ->pluck('template.slug')
            ->toArray();

        $hasExistingComponents = $company
            ? SalaryComponent::where('company_id', $company->id)->exists()
            : SalaryComponent::whereNull('company_id')->exists();

        return view('admin.payroll-templates.index', compact(
            'templates',
            'recommendedSlugs',
            'hasExistingComponents'
        ));
    }

    public function preview(PayrollTemplate $template)
    {
        $user = auth()->user();
        $company = $user->company_id ? $user->company : null;

        $hasExistingComponents = $company
            ? SalaryComponent::where('company_id', $company->id)->exists()
            : SalaryComponent::whereNull('company_id')->exists();

        $defaultMode = $hasExistingComponents ? 'merge' : 'fresh';

        $items = $template->items()->orderBy('execution_order')->get();
        $groupedItems = $items->groupBy('group_label');

        $previewData = $this->templateService->preview($company, $template, $defaultMode);

        return view('admin.payroll-templates.preview', compact(
            'template',
            'groupedItems',
            'hasExistingComponents',
            'defaultMode',
            'previewData'
        ));
    }

    public function apply(Request $request, PayrollTemplate $template)
    {
        $validated = $request->validate([
            'mode' => 'required|in:fresh,merge,replace',
            'excluded_codes' => 'nullable|array',
            'excluded_codes.*' => 'string|max:50',
        ]);

        $user = auth()->user();
        $company = $user->company_id ? $user->company : null;

        $options = [
            'excluded_codes' => $validated['excluded_codes'] ?? [],
        ];

        if ($company) {
            $application = $this->templateService->apply(
                $company,
                $template,
                $validated['mode'],
                $options
            );
        } else {
            $application = $this->templateService->applyWithoutCompany(
                $template,
                $validated['mode'],
                $options
            );
        }

        return view('admin.payroll-templates.result', compact('application', 'template'));
    }

    public function rollback(CompanyTemplateApplication $application)
    {
        $this->templateService->rollback($application);

        return redirect()
            ->route('admin.salary-components.index')
            ->with('success', 'Template berhasil di-rollback. Komponen gaji dikembalikan ke kondisi sebelumnya.');
    }
}
