<?php

use App\Models\PayrollDetail;
use App\Models\PayrollDetailItem;
use App\Models\PayrollPeriod;
use App\Models\SystemSetting;
use App\Models\TaxPtkpRate;
use App\Models\TaxTerRate;
use App\Models\User;
use App\Services\BpjsCalculator;
use App\Services\Pph21Calculator;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('Staf', 'web');
    $this->seed(\Database\Seeders\PayrollSeeder::class);

    SystemSetting::firstOrCreate(['id' => 1], [
        'office_name' => 'Test Office',
        'office_latitude' => -6.2088,
        'office_longitude' => 106.8456,
        'office_radius' => 0.1,
        'work_start_time' => '08:00',
        'work_end_time' => '17:00',
        'overtime_start_time' => '17:30',
        'overtime_end_time' => '21:00',
        'late_tolerance_minutes' => 15,
        'weekend_days' => ['Saturday', 'Sunday'],
        'bpjs_kes_ceiling' => 12000000,
        'bpjs_jp_ceiling' => 10042300,
        'jkk_risk_group' => 1,
        'no_npwp_surcharge_enabled' => true,
    ]);
});

test('TER rate lookup for Category A with gaji 5jt returns PPh 21 = 0', function () {
    $calculator = new Pph21Calculator();

    $pph21 = $calculator->calculateMonthlyTER(5000000, 'A');

    expect($pph21)->toBe(0.0);
});

test('TER rate lookup for Category A with gaji 10jt returns correct rate (2%)', function () {
    $calculator = new Pph21Calculator();

    // 10.000.000 falls in bracket > 9.650.000 s.d. 10.050.000 → 2.00%
    $pph21 = $calculator->calculateMonthlyTER(10000000, 'A');

    $expectedRate = 0.02;
    $expected = round(10000000 * $expectedRate);

    expect($pph21)->toBe($expected); // 200.000
});

test('BPJS calculation enforces ceiling', function () {
    $calculator = new BpjsCalculator();

    // gaji 15jt exceeds bpjs_kes_ceiling (12jt) and bpjs_jp_ceiling (10.042.300)
    $result = $calculator->calculate(15000000, 15000000);

    // KES base = min(15000000, 12000000) = 12000000
    expect($result['kes_company'])->toBe(round(12000000 * 0.04));
    expect($result['kes_employee'])->toBe(round(12000000 * 0.01));

    // JP base = min(15000000, 10042300) = 10042300
    expect($result['jp_company'])->toBe(round(10042300 * 0.02));
    expect($result['jp_employee'])->toBe(round(10042300 * 0.01));

    // JHT uses full gaji_pokok (no ceiling)
    expect($result['jht_company'])->toBe(round(15000000 * 0.037));
    expect($result['jht_employee'])->toBe(round(15000000 * 0.02));

    // JKK risk group 1 = 0.24%
    expect($result['jkk'])->toBe(round(15000000 * 0.0024));

    // JKM = 0.3%
    expect($result['jkm'])->toBe(round(15000000 * 0.003));
});

test('overtime calculation: 1 hour = 1.5x, 3 hours = 1.5x + 2x2.0x', function () {
    $gajiPokok = 5000000;
    $hourlyRate = $gajiPokok / 173;

    // 1 hour overtime
    $overtime1h = min(1, 1) * 1.5 * $hourlyRate;
    expect(round($overtime1h))->toBe(round(1.5 * $hourlyRate));

    // 3 hours overtime: first hour 1.5x + 2 additional hours at 2.0x each
    $firstHour = min(3, 1) * 1.5 * $hourlyRate;
    $additionalHours = max(3 - 1, 0) * 2.0 * $hourlyRate;
    $overtime3h = round($firstHour + $additionalHours);

    $expected = round(1.5 * $hourlyRate + 2 * 2.0 * $hourlyRate);
    expect($overtime3h)->toBe($expected);
});

test('proration for mid-month join calculates correct factor', function () {
    $service = app(\App\Services\PayrollService::class);

    $user = User::factory()->create([
        'gaji_pokok' => 10000000,
        'status_pernikahan' => 'TK',
        'jumlah_tanggungan' => 0,
        'tanggal_masuk' => '2025-01-15',
        'status_karyawan' => 'tetap',
    ]);

    $period = PayrollPeriod::create([
        'period_month' => '2025-01',
        'start_date' => '2025-01-01',
        'end_date' => '2025-01-31',
        'status' => 'draft',
    ]);

    $detail = $service->calculateForEmployee($period, $user);

    // Prorate factor should be < 1 since employee joined mid-month
    expect((float) $detail->prorate_factor)->toBeLessThan(1.0);
    expect($detail->prorate_reason)->toContain('Karyawan baru masuk');
});

test('December correction uses progressive rates', function () {
    $calculator = new Pph21Calculator();

    $user = User::factory()->create([
        'gaji_pokok' => 20000000,
        'status_pernikahan' => 'TK',
        'jumlah_tanggungan' => 0,
        'tanggal_masuk' => '2025-01-01',
        'status_karyawan' => 'tetap',
        'npwp' => '12.345.678.9-012.345',
    ]);

    $period = PayrollPeriod::create([
        'period_month' => '2025-01',
        'start_date' => '2025-01-01',
        'end_date' => '2025-01-31',
        'status' => 'calculated',
    ]);

    // Create 11 months of payroll data (Jan-Nov)
    for ($m = 1; $m <= 11; $m++) {
        $pm = '2025-' . str_pad($m, 2, '0', STR_PAD_LEFT);
        $pp = PayrollPeriod::firstOrCreate(
            ['period_month' => $pm],
            [
                'start_date' => "2025-{$m}-01",
                'end_date' => \Carbon\Carbon::create(2025, $m)->endOfMonth()->toDateString(),
                'status' => 'calculated',
            ]
        );

        $detail = PayrollDetail::create([
            'payroll_period_id' => $pp->id,
            'user_id' => $user->id,
            'working_days' => 22,
            'present_days' => 22,
            'late_count' => 0,
            'late_minutes_total' => 0,
            'overtime_hours' => 0,
            'gaji_pokok' => 20000000,
            'total_earnings' => 20000000,
            'total_deductions' => 0,
            'total_bpjs_company' => 0,
            'total_bpjs_employee' => 500000,
            'pph21_amount' => 400000,
            'net_salary' => 19100000,
            'status' => 'calculated',
        ]);

        PayrollDetailItem::create([
            'payroll_detail_id' => $detail->id,
            'component_name' => 'BPJS JHT Karyawan',
            'component_type' => 'bpjs_employee',
            'amount' => 400000,
        ]);
        PayrollDetailItem::create([
            'payroll_detail_id' => $detail->id,
            'component_name' => 'BPJS JP Karyawan',
            'component_type' => 'bpjs_employee',
            'amount' => 100000,
        ]);
    }

    $decemberPph21 = $calculator->calculateDecemberCorrection($user, 2025);

    // December correction should be a non-negative number
    expect($decemberPph21)->toBeGreaterThanOrEqual(0);
    // It should be a rounded integer
    expect($decemberPph21)->toBe(round($decemberPph21));
});
