<?php

namespace Database\Seeders;

use App\Models\BpjsRateVersion;
use App\Models\SalaryComponent;
use App\Models\TaxPtkpRate;
use App\Models\TaxPtkpRateVersion;
use App\Models\TaxTerRate;
use App\Models\TaxTerRateVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DynamicPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $this->backfillCategories();
        $this->backfillExecutionOrder();
        $this->seedBpjsRateVersions();
        $this->migrateTerRates();
        $this->migratePtkpRates();
    }

    private function backfillCategories(): void
    {
        if (!Schema::hasColumn('salary_components', 'category')) {
            return;
        }

        $mapping = [
            'GAJI_POKOK' => 'basic',
        ];

        foreach ($mapping as $code => $category) {
            SalaryComponent::where('code', $code)->update(['category' => $category]);
        }

        SalaryComponent::where('code', 'like', 'TUNJ_%')
            ->where('category', 'earning')
            ->update(['category' => 'earning']);

        SalaryComponent::where('code', 'like', 'POT_%')
            ->update(['category' => 'deduction']);

        SalaryComponent::where('code', 'UANG_LEMBUR')
            ->update(['category' => 'earning']);
    }

    private function backfillExecutionOrder(): void
    {
        if (!Schema::hasColumn('salary_components', 'execution_order')) {
            return;
        }

        $orderMap = [
            'GAJI_POKOK' => 10,
            'TUNJ_JABATAN' => 20,
            'TUNJ_TRANSPORT' => 30,
            'TUNJ_MAKAN' => 40,
            'TUNJ_KEHADIRAN' => 50,
            'UANG_LEMBUR' => 60,
            'POT_TERLAMBAT' => 70,
            'POT_PELANGGARAN' => 80,
            'POT_CUTI_UNPAID' => 90,
        ];

        foreach ($orderMap as $code => $order) {
            SalaryComponent::where('code', $code)->update(['execution_order' => $order]);
        }
    }

    private function seedBpjsRateVersions(): void
    {
        $rates = [
            ['program' => 'jht', 'employer_rate' => 3.70000, 'employee_rate' => 2.00000, 'max_salary_basis' => null, 'min_salary_basis' => null],
            ['program' => 'jkk', 'employer_rate' => 0.24000, 'employee_rate' => 0.00000, 'max_salary_basis' => null, 'min_salary_basis' => null],
            ['program' => 'jkm', 'employer_rate' => 0.30000, 'employee_rate' => 0.00000, 'max_salary_basis' => null, 'min_salary_basis' => null],
            ['program' => 'jp', 'employer_rate' => 2.00000, 'employee_rate' => 1.00000, 'max_salary_basis' => 10042300, 'min_salary_basis' => null],
            ['program' => 'bpjs_kesehatan', 'employer_rate' => 4.00000, 'employee_rate' => 1.00000, 'max_salary_basis' => 12000000, 'min_salary_basis' => null],
        ];

        foreach ($rates as $rate) {
            BpjsRateVersion::updateOrCreate(
                [
                    'program' => $rate['program'],
                    'effective_from' => '2024-01-01',
                    'company_id' => null,
                ],
                array_merge($rate, [
                    'effective_from' => '2024-01-01',
                    'effective_until' => null,
                    'notes' => 'National default rates',
                ])
            );
        }
    }

    private function migrateTerRates(): void
    {
        if (!Schema::hasTable('tax_ter_rates')) {
            return;
        }

        if (TaxTerRateVersion::where('regulation_code', 'PP_58_2023')->exists()) {
            return;
        }

        $existingRates = TaxTerRate::all();

        if ($existingRates->isEmpty()) {
            return;
        }

        $rows = [];
        $now = now();

        foreach ($existingRates as $rate) {
            $rows[] = [
                'regulation_code' => 'PP_58_2023',
                'category' => $rate->category,
                'min_income' => $rate->min_income,
                'max_income' => $rate->max_income,
                'rate' => $rate->rate,
                'effective_from' => '2024-01-01',
                'effective_until' => null,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            TaxTerRateVersion::insert($chunk);
        }
    }

    private function migratePtkpRates(): void
    {
        if (!Schema::hasTable('tax_ptkp_rates')) {
            return;
        }

        if (TaxPtkpRateVersion::where('effective_from', '2024-01-01')->exists()) {
            return;
        }

        $existingRates = TaxPtkpRate::all();

        if ($existingRates->isEmpty()) {
            return;
        }

        $now = now();

        foreach ($existingRates as $rate) {
            TaxPtkpRateVersion::create([
                'status' => $rate->status,
                'amount' => $rate->amount,
                'effective_from' => '2024-01-01',
                'effective_until' => null,
                'created_by' => null,
            ]);
        }
    }
}
