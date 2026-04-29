<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCompanyController extends Controller
{
    public function __construct(
        protected CompanyService $companyService
    ) {}

    public function index(Request $request)
    {
        $query = Company::withCount(['branches', 'employees']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $companies = $query->latest()->paginate(12)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        $parentCompanies = Company::active()->whereNull('parent_id')->orderBy('name')->get();

        return view('admin.companies.create', compact('parentCompanies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:companies,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'npwp' => 'nullable|string|max:30',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'is_active' => 'boolean',
            'is_headquarters' => 'boolean',
            'parent_id' => 'nullable|exists:companies,id',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_headquarters'] = $request->boolean('is_headquarters', false);

        Company::create($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function show(Company $company)
    {
        $company->load(['branches' => function ($q) {
            $q->withCount('employees');
        }, 'employees', 'parent', 'children']);

        $employeeCount = $company->employees->count();
        $branchCount = $company->branches->count();
        $settings = $this->companyService->getCompanySettings($company);

        return view('admin.companies.show', compact('company', 'employeeCount', 'branchCount', 'settings'));
    }

    public function edit(Company $company)
    {
        $parentCompanies = Company::active()
            ->where('id', '!=', $company->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.companies.edit', compact('company', 'parentCompanies'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:companies,code,' . $company->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'npwp' => 'nullable|string|max:30',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'is_active' => 'boolean',
            'is_headquarters' => 'boolean',
            'parent_id' => 'nullable|exists:companies,id',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_headquarters'] = $request->boolean('is_headquarters', false);

        $company->update($validated);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company)
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }

    public function storeBranch(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:company_branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'integer|min:10|max:10000',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $company->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        CompanyBranch::create($validated);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function updateBranch(Request $request, CompanyBranch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:company_branches,code,' . $branch->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'integer|min:10|max:10000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $branch->update($validated);

        return redirect()->route('admin.companies.show', $branch->company_id)
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroyBranch(CompanyBranch $branch)
    {
        $companyId = $branch->company_id;
        $branch->delete();

        return redirect()->route('admin.companies.show', $companyId)
            ->with('success', 'Cabang berhasil dihapus.');
    }

    public function settings(Company $company)
    {
        $settings = $this->companyService->getCompanySettings($company);

        return view('admin.companies.settings', compact('company', 'settings'));
    }

    public function updateSettings(Request $request, Company $company)
    {
        $validated = $request->validate([
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'late_tolerance' => 'nullable|integer|min:0|max:120',
            'office_latitude' => 'nullable|numeric|between:-90,90',
            'office_longitude' => 'nullable|numeric|between:-180,180',
            'office_radius' => 'nullable|integer|min:10|max:10000',
        ]);

        $settings = array_filter($validated, fn ($v) => $v !== null && $v !== '');

        $company->update(['settings' => $settings]);

        return redirect()->route('admin.companies.settings', $company)
            ->with('success', 'Pengaturan perusahaan berhasil diperbarui.');
    }

    public function assignEmployee(Request $request, Company $company)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:company_branches,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        $this->companyService->transferEmployee(
            $user,
            $company,
            isset($validated['branch_id']) ? CompanyBranch::find($validated['branch_id']) : null
        );

        return redirect()->route('admin.companies.show', $company)
            ->with('success', "Karyawan {$user->name} berhasil ditugaskan.");
    }

    public function transferEmployee(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'nullable|exists:company_branches,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $company = Company::findOrFail($validated['company_id']);
        $branch = isset($validated['branch_id']) ? CompanyBranch::find($validated['branch_id']) : null;

        $this->companyService->transferEmployee($user, $company, $branch);

        return redirect()->back()
            ->with('success', "Karyawan {$user->name} berhasil dipindahkan ke {$company->name}.");
    }
}
