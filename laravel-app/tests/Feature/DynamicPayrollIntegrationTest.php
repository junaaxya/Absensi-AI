<?php

use App\Models\Attendance;
use App\Models\BpjsRateVersion;
use App\Models\PayrollPeriod;
use App\Models\PayrollSnapshot;
use App\Models\SalaryComponent;
use App\Models\SystemSetting;
use App\Models\TaxTerRateVersion;
use App\Models\User;
use App\Services\Payroll\BpjsRateResolver;
use App\Services\Payroll\ComponentResolver;
use App\Services\Payroll\ContextBuilder;
use App\Services\Payroll\SnapshotService;
use App\Services\Payroll\TaxRateResolver;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('Staf', 'web');

    SystemSetting::firstOrCreate(['id' => 1], [
        'office_name' => 'Test Office',
        'office_latitude' => -6.2088,
        'office_longitude' => 106.8456,
        'office_radius' => 0.1,
        'work_start_time' => '08:00:00',
        'work_end_time' => '17:00:00',
        'overtime_start_time' => '17:30:00',
        'overtime_end_time' => '21:00:00',
        'late_tolerance_minutes' => 15,
        'weekend_days' => ['Saturday', 'Sunday'],
        'bpjs_kes_ceiling' => 12000000,
        'bpjs_jp_ceiling' => 10042300,
        'jkk_risk_group' => 1,
    ]);
});

test('ComponentResolver returns components ordered by execution_order', function () {
    SalaryComponent::create([
        'name' => 'Komponen C',
        'code' => 'COMP_C',
        'type' => 'earning',
        'category' => 'earning',
        'execution_order' => 30,
        'is_active' => true,
        'value_type' => 'flat',
        'default_amount' => 100000,
    ]);

    SalaryComponent::create([
        'name' => 'Komponen A',
        'code' => 'COMP_A',
        'type' => 'earning',
        'category' => 'basic',
        'execution_order' => 10,
        'is_active' => true,
        'value_type' => 'flat',
        'default_amount' => 5000000,
    ]);

    SalaryComponent::create([
        'name' => 'Komponen B',
        'code' => 'COMP_B',
        'type' => 'deduction',
        'category' => 'deduction',
        'execution_order' => 20,
        'is_active' => true,
        'value_type' => 'formula',
        'formula' => 'basic_salary * 0.02',
        'default_amount' => 0,
    ]);

    $resolver = app(ComponentResolver::class);
    $components = $resolver->resolve(0, Carbon::parse('2025-01-31'));

    expect($components)->toHaveCount(3);
    expect($components[0]->code)->toBe('COMP_A');
    expect($components[1]->code)->toBe('COMP_B');
    expect($components[2]->code)->toBe('COMP_C');
});

test('ContextBuilder builds correct context from attendance data', function () {
    $user = User::factory()->create([
        'gaji_pokok' => 8000000,
        'tanggal_masuk' => '2024-01-01',
        'status_karyawan' => 'tetap',
    ]);

    $period = PayrollPeriod::create([
        'period_month' => '2025-01',
        'start_date' => '2025-01-01',
        'end_date' => '2025-01-31',
        'status' => 'draft',
    ]);

    for ($day = 2; $day <= 24; $day++) {
        $date = Carbon::create(2025, 1, $day);
        if ($date->isWeekend()) {
            continue;
        }
        Attendance::create([
            'user_id' => $user->id,
            'tanggal' => $date->toDateString(),
            'jam_masuk' => $date->copy()->setTime(8, 0, 0),
            'jam_keluar' => $date->copy()->setTime(17, 0, 0),
            'status' => 'hadir',
        ]);
    }

    $builder = app(ContextBuilder::class);
    $context = $builder->build($user, $period);

    expect($context['working_days'])->toBeGreaterThan(0);
    expect($context['present_days'])->toBeGreaterThan(0);
    expect($context['basic_salary'])->toBe(8000000.0);
    expect($context['prorate_factor'])->toBe(1.0);
    expect($context['late_count'])->toBe(0);
});

test('TaxRateResolver returns correct TER rate for specific date', function () {
    TaxTerRateVersion::create([
        'regulation_code' => 'PP-58-2023',
        'category' => 'A',
        'min_income' => 0,
        'max_income' => 5400000,
        'rate' => 0,
        'effective_from' => '2024-01-01',
        'effective_until' => null,
    ]);

    TaxTerRateVersion::create([
        'regulation_code' => 'PP-58-2023',
        'category' => 'A',
        'min_income' => 5400001,
        'max_income' => 10000000,
        'rate' => 2.0,
        'effective_from' => '2024-01-01',
        'effective_until' => '2025-12-31',
    ]);

    TaxTerRateVersion::create([
        'regulation_code' => 'PP-NEW-2026',
        'category' => 'A',
        'min_income' => 5400001,
        'max_income' => 10000000,
        'rate' => 2.5,
        'effective_from' => '2026-01-01',
        'effective_until' => null,
    ]);

    $resolver = app(TaxRateResolver::class);

    $rate2025 = $resolver->getTerRate('A', 8000000, Carbon::parse('2025-06-15'));
    expect($rate2025)->toBe(2.0);

    $rate2026 = $resolver->getTerRate('A', 8000000, Carbon::parse('2026-06-15'));
    expect($rate2026)->toBe(2.5);
});

test('BpjsRateResolver returns correct rates for specific date', function () {
    BpjsRateVersion::create([
        'program' => 'jht',
        'employer_rate' => 3.7,
        'employee_rate' => 2.0,
        'max_salary_basis' => null,
        'min_salary_basis' => null,
        'effective_from' => '2024-01-01',
        'effective_until' => null,
    ]);

    BpjsRateVersion::create([
        'program' => 'bpjs_kesehatan',
        'employer_rate' => 4.0,
        'employee_rate' => 1.0,
        'max_salary_basis' => 12000000,
        'min_salary_basis' => null,
        'effective_from' => '2024-01-01',
        'effective_until' => null,
    ]);

    $resolver = app(BpjsRateResolver::class);
    $rates = $resolver->getRates(Carbon::parse('2025-01-31'));

    expect($rates['jht']['employer_rate'])->toEqualWithDelta(0.037, 0.0001);
    expect($rates['jht']['employee_rate'])->toEqualWithDelta(0.02, 0.0001);
    expect($rates['bpjs_kesehatan']['employer_rate'])->toEqualWithDelta(0.04, 0.0001);
    expect($rates['bpjs_kesehatan']['employee_rate'])->toEqualWithDelta(0.01, 0.0001);
    expect($rates['bpjs_kesehatan']['max_salary_basis'])->toEqualWithDelta(12000000.0, 0.01);
});

test('SnapshotService captures and verifies snapshot integrity', function () {
    $this->seed(\Database\Seeders\PayrollSeeder::class);

    $period = PayrollPeriod::create([
        'period_month' => '2025-01',
        'start_date' => '2025-01-01',
        'end_date' => '2025-01-31',
        'status' => 'approved',
    ]);

    $snapshotService = app(SnapshotService::class);
    $snapshotService->captureFullSnapshot($period);

    $snapshots = PayrollSnapshot::where('payroll_period_id', $period->id)->get();
    expect($snapshots->count())->toBeGreaterThanOrEqual(3);

    $salarySnapshot = $snapshots->firstWhere('snapshot_type', 'salary_components');
    expect($salarySnapshot)->not->toBeNull();
    expect($salarySnapshot->snapshot_data)->toBeArray();
    expect($salarySnapshot->snapshot_hash)->not->toBeEmpty();

    $expectedHash = hash('sha256', json_encode($salarySnapshot->snapshot_data));
    expect($salarySnapshot->snapshot_hash)->toBe($expectedHash);

    $diffs = $snapshotService->verifySnapshot($period);
    expect($diffs)->toBeEmpty();
});
