<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CompanyService
{
    /** Company settings override global system_settings. */
    public function getCompanySettings(Company $company): array
    {
        $global = SystemSetting::first();
        $globalSettings = [];

        if ($global) {
            $globalSettings = [
                'work_start' => $global->work_start_time,
                'work_end' => $global->work_end_time,
                'late_tolerance' => $global->late_tolerance,
                'office_latitude' => $global->office_latitude,
                'office_longitude' => $global->office_longitude,
                'office_radius' => $global->office_radius,
            ];
        }

        $companySettings = $company->settings ?? [];

        return array_merge($globalSettings, $companySettings);
    }

    public function getEmployeesByCompany(Company $company): Collection
    {
        return User::where('company_id', $company->id)->get();
    }

    public function getEmployeesByBranch(CompanyBranch $branch): Collection
    {
        return User::where('branch_id', $branch->id)->get();
    }

    public function transferEmployee(User $user, Company $company, ?CompanyBranch $branch = null): User
    {
        $user->update([
            'company_id' => $company->id,
            'branch_id' => $branch?->id,
        ]);

        return $user->fresh();
    }
}
