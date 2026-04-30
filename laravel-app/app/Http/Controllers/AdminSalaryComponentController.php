<?php

namespace App\Http\Controllers;

use App\Exceptions\FormulaException;
use App\Models\FormulaAuditLog;
use App\Models\SalaryComponent;
use App\Services\Payroll\Formula\FormulaEvaluator;
use App\Services\Payroll\Formula\VariableRegistry;
use Illuminate\Http\Request;

class AdminSalaryComponentController extends Controller
{
    public function index()
    {
        $components = SalaryComponent::orderBy('execution_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('admin.salary-components.index', compact('components'));
    }

    public function create()
    {
        return view('admin.salary-components.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:salary_components,code',
            'type' => 'required|in:earning,deduction,benefit',
            'is_taxable' => 'nullable|boolean',
            'is_fixed' => 'nullable|boolean',
            'default_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'value_type' => 'nullable|in:flat,formula',
            'formula' => 'nullable|string|max:500',
            'execution_order' => 'nullable|integer|min:0',
            'min_value' => 'nullable|numeric|min:0',
            'max_value' => 'nullable|numeric|min:0',
            'category' => 'nullable|in:basic,earning,deduction,benefit,bpjs_employee,bpjs_company,tax',
            'is_prorated' => 'nullable|boolean',
            'prorate_basis' => 'nullable|in:working_days,calendar_days',
        ]);

        $validated['is_taxable'] = $request->boolean('is_taxable');
        $validated['is_fixed'] = $request->boolean('is_fixed', true);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_prorated'] = $request->boolean('is_prorated');
        $validated['default_amount'] = $validated['default_amount'] ?? 0;
        $validated['value_type'] = $validated['value_type'] ?? 'flat';
        $validated['execution_order'] = $validated['execution_order'] ?? 100;

        if (($validated['value_type'] ?? 'flat') === 'formula' && !empty($validated['formula'])) {
            $evaluator = new FormulaEvaluator();
            $result = $evaluator->validate($validated['formula'], VariableRegistry::availableNames());
            if (!$result['valid']) {
                return back()->withErrors(['formula' => $result['error']])->withInput();
            }
        }

        $component = SalaryComponent::create($validated);

        if (!empty($validated['formula'])) {
            $this->logFormulaChange('create', $component, null, $validated['formula']);
        }

        return redirect()
            ->route('admin.salary-components.index')
            ->with('success', 'Komponen gaji berhasil ditambahkan.');
    }

    public function edit(SalaryComponent $salaryComponent)
    {
        return view('admin.salary-components.edit', compact('salaryComponent'));
    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:salary_components,code,' . $salaryComponent->id,
            'type' => 'required|in:earning,deduction,benefit',
            'is_taxable' => 'nullable|boolean',
            'is_fixed' => 'nullable|boolean',
            'default_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'value_type' => 'nullable|in:flat,formula',
            'formula' => 'nullable|string|max:500',
            'execution_order' => 'nullable|integer|min:0',
            'min_value' => 'nullable|numeric|min:0',
            'max_value' => 'nullable|numeric|min:0',
            'category' => 'nullable|in:basic,earning,deduction,benefit,bpjs_employee,bpjs_company,tax',
            'is_prorated' => 'nullable|boolean',
            'prorate_basis' => 'nullable|in:working_days,calendar_days',
        ]);

        $validated['is_taxable'] = $request->boolean('is_taxable');
        $validated['is_fixed'] = $request->boolean('is_fixed', true);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_prorated'] = $request->boolean('is_prorated');
        $validated['default_amount'] = $validated['default_amount'] ?? 0;
        $validated['value_type'] = $validated['value_type'] ?? 'flat';
        $validated['execution_order'] = $validated['execution_order'] ?? $salaryComponent->execution_order ?? 100;

        if (($validated['value_type'] ?? 'flat') === 'formula' && !empty($validated['formula'])) {
            $evaluator = new FormulaEvaluator();
            $result = $evaluator->validate($validated['formula'], VariableRegistry::availableNames());
            if (!$result['valid']) {
                return back()->withErrors(['formula' => $result['error']])->withInput();
            }
        }

        $oldFormula = $salaryComponent->formula;
        $newFormula = $validated['formula'] ?? null;

        $salaryComponent->update($validated);

        if ($oldFormula !== $newFormula) {
            $this->logFormulaChange('update', $salaryComponent, $oldFormula, $newFormula);
        }

        return redirect()
            ->route('admin.salary-components.index')
            ->with('success', 'Komponen gaji berhasil diperbarui.');
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        if ($salaryComponent->formula) {
            $this->logFormulaChange('delete', $salaryComponent, $salaryComponent->formula, null);
        }

        $salaryComponent->delete();

        return redirect()
            ->route('admin.salary-components.index')
            ->with('success', 'Komponen gaji berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:salary_components,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->input('items') as $item) {
            SalaryComponent::where('id', $item['id'])->update(['execution_order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan berhasil disimpan.',
        ]);
    }

    public function validateFormula(Request $request)
    {
        $request->validate([
            'formula' => 'required|string',
        ]);

        $formula = $request->input('formula');
        $evaluator = new FormulaEvaluator();
        $result = $evaluator->validate($formula, VariableRegistry::availableNames());

        return response()->json([
            'valid' => $result['valid'],
            'error' => $result['error'],
            'variables_used' => $result['variables_used'],
        ]);
    }

    public function previewFormula(Request $request)
    {
        $request->validate([
            'formula' => 'required|string',
            'context' => 'nullable|array',
        ]);

        $formula = $request->input('formula');
        $context = $request->input('context', []);

        $sampleVariables = array_merge([
            'basic_salary' => 8000000,
            'working_days' => 22,
            'present_days' => 22,
            'absent_days' => 0,
            'late_count' => 0,
            'overtime_hours' => 0,
            'leave_days_unpaid' => 0,
            'sick_days' => 0,
            'gross_salary' => 10000000,
            'prorate_factor' => 1.0,
            'bpjs_basis_salary' => 8000000,
            'taxable_income' => 8000000,
            'ter_rate' => 0.02,
            'ptkp_status' => 1,
            'ptkp_amount' => 54000000,
        ], $context);

        $evaluator = new FormulaEvaluator();

        try {
            $result = $evaluator->evaluate($formula, $sampleVariables);

            return response()->json([
                'success' => true,
                'result' => $result,
                'formatted_result' => 'Rp ' . number_format($result, 0, ',', '.'),
                'variables_used' => $evaluator->validate($formula, VariableRegistry::availableNames())['variables_used'],
            ]);
        } catch (FormulaException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'result' => null,
                'formatted_result' => null,
            ], 422);
        }
    }

    public function variables()
    {
        return response()->json(VariableRegistry::definitions());
    }

    private function logFormulaChange(string $action, SalaryComponent $component, ?string $oldFormula, ?string $newFormula): void
    {
        FormulaAuditLog::create([
            'user_id' => auth()->id() ?? 0,
            'action' => $action,
            'entity_type' => 'salary_component',
            'entity_id' => $component->id,
            'old_formula' => $oldFormula,
            'new_formula' => $newFormula,
            'ip_address' => request()->ip(),
            'metadata' => ['component_code' => $component->code],
            'created_at' => now(),
        ]);
    }
}
