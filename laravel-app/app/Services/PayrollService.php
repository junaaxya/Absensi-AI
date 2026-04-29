<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\EmployeeSalaryComponent;
use App\Models\Holiday;
use App\Models\Izin;
use App\Models\LeaveType;
use App\Models\PayrollDetail;
use App\Models\PayrollDetailItem;
use App\Models\PayrollPeriod;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollService
{
    private const DAY_NAME_TO_NUMBER = [
        'Sunday'    => 0,
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6,
    ];

    public function __construct(
        private BpjsCalculator $bpjsCalculator,
        private Pph21Calculator $pph21Calculator,
        private ViolationService $violationService,
    ) {}

    public function createPeriod(string $yearMonth): PayrollPeriod
    {
        $startDate = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return PayrollPeriod::create([
            'period_month' => $yearMonth,
            'start_date'   => $startDate->toDateString(),
            'end_date'     => $endDate->toDateString(),
            'status'       => 'draft',
        ]);
    }

    public function calculateForEmployee(PayrollPeriod $period, User $user): PayrollDetail
    {
        $settings = SystemSetting::first();
        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);

        $workingDays = $this->countWorkingDays($startDate, $endDate, $settings);

        $attendance = $this->countAttendance($user, $startDate, $endDate, $settings);

        [$prorateFactor, $prorateReason] = $this->calculateProration(
            $user, $startDate, $endDate, $workingDays
        );

        $gajiPokok = (float) $user->gaji_pokok;
        $proratedGaji = $gajiPokok * $prorateFactor;

        // Step B: Gross earnings
        $earningItems = [];
        $earningItems[] = ['name' => 'Gaji Pokok', 'amount' => $proratedGaji];

        $fixedEarningComponents = EmployeeSalaryComponent::where('user_id', $user->id)
            ->whereHas('salaryComponent', fn ($q) => $q->where('type', 'earning')->where('is_active', true))
            ->with('salaryComponent')
            ->get();

        $totalFixedEarnings = $proratedGaji;
        foreach ($fixedEarningComponents as $esc) {
            $amount = (float) $esc->amount * $prorateFactor;
            $earningItems[] = ['name' => $esc->salaryComponent->name, 'amount' => $amount];
            if ($esc->salaryComponent->is_fixed) {
                $totalFixedEarnings += $amount;
            }
        }

        // Overtime: upah_sejam = gaji_pokok / 173 (UU Cipta Kerja)
        $overtimeHours = $attendance['overtime_hours'];
        $overtimePay = 0;
        if ($overtimeHours > 0) {
            $hourlyRate = $gajiPokok / 173;
            $firstHour = min($overtimeHours, 1) * 1.5 * $hourlyRate;
            $additionalHours = max($overtimeHours - 1, 0) * 2.0 * $hourlyRate;
            $overtimePay = round($firstHour + $additionalHours);
            $earningItems[] = ['name' => 'Uang Lembur', 'amount' => $overtimePay];
        }

        $totalEarnings = array_sum(array_column($earningItems, 'amount'));

        // Step C: Deductions
        $deductionItems = [];

        $violationDeduction = $this->violationService->getPayrollDeduction($user, $period->period_month);
        if ($violationDeduction > 0) {
            $deductionItems[] = ['name' => 'Potongan Pelanggaran', 'amount' => $violationDeduction];
        }

        $unpaidLeaveDeduction = $this->calculateUnpaidLeaveDeduction(
            $user, $startDate, $endDate, $gajiPokok, $workingDays
        );
        if ($unpaidLeaveDeduction > 0) {
            $deductionItems[] = ['name' => 'Potongan Cuti Tidak Berbayar', 'amount' => $unpaidLeaveDeduction];
        }

        $totalDeductions = array_sum(array_column($deductionItems, 'amount'));

        // Step D: BPJS
        $bpjs = $this->bpjsCalculator->calculate($proratedGaji, $totalFixedEarnings);

        // Step E: PPh 21
        $isDecember = str_ends_with($period->period_month, '-12');
        $pph21Amount = 0;

        if ($isDecember) {
            $year = (int) substr($period->period_month, 0, 4);
            $pph21Amount = $this->pph21Calculator->calculateDecemberCorrection($user, $year);
        } else {
            $terCategory = $user->kategori_ter;
            if ($terCategory) {
                $pph21Amount = $this->pph21Calculator->calculateMonthlyTER($totalEarnings, $terCategory);
            }
        }

        // Step F: Net salary
        $netSalary = $totalEarnings - $totalDeductions - $bpjs['total_employee'] - $pph21Amount;

        // Step G: Save
        return DB::transaction(function () use (
            $period, $user, $workingDays, $attendance, $gajiPokok,
            $totalEarnings, $totalDeductions, $bpjs, $pph21Amount, $netSalary,
            $prorateFactor, $prorateReason, $earningItems, $deductionItems, $overtimeHours
        ) {
            PayrollDetail::where('payroll_period_id', $period->id)
                ->where('user_id', $user->id)
                ->delete();

            $detail = PayrollDetail::create([
                'payroll_period_id' => $period->id,
                'user_id'           => $user->id,
                'working_days'      => $workingDays,
                'present_days'      => $attendance['present_days'],
                'late_count'        => $attendance['late_count'],
                'late_minutes_total' => $attendance['late_minutes_total'],
                'overtime_hours'    => $overtimeHours,
                'gaji_pokok'        => $gajiPokok,
                'total_earnings'    => $totalEarnings,
                'total_deductions'  => $totalDeductions,
                'total_bpjs_company'  => $bpjs['total_company'],
                'total_bpjs_employee' => $bpjs['total_employee'],
                'pph21_amount'      => $pph21Amount,
                'net_salary'        => $netSalary,
                'prorate_factor'    => $prorateFactor,
                'prorate_reason'    => $prorateReason,
                'status'            => 'calculated',
            ]);

            foreach ($earningItems as $item) {
                PayrollDetailItem::create([
                    'payroll_detail_id' => $detail->id,
                    'component_name'    => $item['name'],
                    'component_type'    => 'earning',
                    'amount'            => $item['amount'],
                ]);
            }

            foreach ($deductionItems as $item) {
                PayrollDetailItem::create([
                    'payroll_detail_id' => $detail->id,
                    'component_name'    => $item['name'],
                    'component_type'    => 'deduction',
                    'amount'            => $item['amount'],
                ]);
            }

            $bpjsCompanyItems = [
                ['name' => 'BPJS Kesehatan Perusahaan', 'amount' => $bpjs['kes_company']],
                ['name' => 'BPJS JHT Perusahaan',       'amount' => $bpjs['jht_company']],
                ['name' => 'BPJS JKK',                  'amount' => $bpjs['jkk']],
                ['name' => 'BPJS JKM',                  'amount' => $bpjs['jkm']],
                ['name' => 'BPJS JP Perusahaan',         'amount' => $bpjs['jp_company']],
            ];

            foreach ($bpjsCompanyItems as $item) {
                PayrollDetailItem::create([
                    'payroll_detail_id' => $detail->id,
                    'component_name'    => $item['name'],
                    'component_type'    => 'bpjs_company',
                    'amount'            => $item['amount'],
                ]);
            }

            $bpjsEmployeeItems = [
                ['name' => 'BPJS Kesehatan Karyawan', 'amount' => $bpjs['kes_employee']],
                ['name' => 'BPJS JHT Karyawan',       'amount' => $bpjs['jht_employee']],
                ['name' => 'BPJS JP Karyawan',         'amount' => $bpjs['jp_employee']],
            ];

            foreach ($bpjsEmployeeItems as $item) {
                PayrollDetailItem::create([
                    'payroll_detail_id' => $detail->id,
                    'component_name'    => $item['name'],
                    'component_type'    => 'bpjs_employee',
                    'amount'            => $item['amount'],
                ]);
            }

            if ($pph21Amount > 0) {
                PayrollDetailItem::create([
                    'payroll_detail_id' => $detail->id,
                    'component_name'    => 'PPh 21',
                    'component_type'    => 'tax',
                    'amount'            => $pph21Amount,
                ]);
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
                Log::info("Payroll: skipping {$employee->name} (ID:{$employee->id}) — not payroll-ready");
                continue;
            }

            try {
                $detail = $this->calculateForEmployee($period, $employee);
                $calculatedCount++;
                $totalGross += (float) $detail->total_earnings;
                $totalDeductions += (float) $detail->total_deductions
                    + (float) $detail->total_bpjs_employee
                    + (float) $detail->pph21_amount;
                $totalNet += (float) $detail->net_salary;
            } catch (\Throwable $e) {
                Log::error("Payroll: failed for {$employee->name} (ID:{$employee->id}): {$e->getMessage()}");
            }
        }

        $period->update([
            'status'          => 'calculated',
            'total_employees' => $calculatedCount,
            'total_gross'     => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net'       => $totalNet,
            'calculated_at'   => now(),
        ]);
    }

    public function approvePeriod(PayrollPeriod $period, User $approver): void
    {
        $period->update([
            'status'      => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $period->payrollDetails()->update(['status' => 'approved']);
    }

    public function markAsPaid(PayrollPeriod $period): void
    {
        $period->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        $period->payrollDetails()->update(['status' => 'paid']);
    }

    private function countWorkingDays(Carbon $startDate, Carbon $endDate, ?SystemSetting $settings): int
    {
        $weekendDayNames = $settings->weekend_days ?? ['Saturday', 'Sunday'];
        $weekendNumbers = array_map(
            fn ($name) => self::DAY_NAME_TO_NUMBER[$name] ?? null,
            $weekendDayNames
        );
        $weekendNumbers = array_filter($weekendNumbers, fn ($v) => $v !== null);

        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $workingDays = 0;
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            if (in_array($date->dayOfWeek, $weekendNumbers)) {
                continue;
            }
            if (in_array($date->toDateString(), $holidays)) {
                continue;
            }
            $workingDays++;
        }

        return $workingDays;
    }

    private function countAttendance(User $user, Carbon $startDate, Carbon $endDate, ?SystemSetting $settings): array
    {
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $presentDays = $attendances->count();
        $lateCount = $attendances->where('status', 'terlambat')->count();

        $lateMinutesTotal = 0;
        $shift = $user->shift;
        $workStartTime = $shift ? $shift->start_time : ($settings->work_start_time ?? '08:00:00');
        $lateTolerance = $settings->late_tolerance_minutes ?? 15;

        foreach ($attendances->where('status', 'terlambat') as $att) {
            $batasMasuk = Carbon::createFromFormat('H:i:s', $workStartTime)->addMinutes($lateTolerance);
            $jamMasuk = Carbon::createFromFormat('H:i:s', $att->jam_masuk->format('H:i:s'));
            $lateMinutesTotal += max(0, $jamMasuk->diffInMinutes($batasMasuk, false));
        }

        $overtimeHours = 0.0;
        $overtimeStartTime = $settings->overtime_start_time ?? null;

        if ($overtimeStartTime) {
            foreach ($attendances as $att) {
                if (!$att->jam_keluar) {
                    continue;
                }
                $checkout = Carbon::createFromFormat('H:i:s', $att->jam_keluar->format('H:i:s'));
                $otStart = Carbon::createFromFormat('H:i:s', $overtimeStartTime);

                if ($checkout->greaterThan($otStart)) {
                    $otEnd = $settings->overtime_end_time
                        ? Carbon::createFromFormat('H:i:s', $settings->overtime_end_time)
                        : $checkout;
                    $effectiveEnd = $checkout->lessThan($otEnd) ? $checkout : $otEnd;
                    $overtimeHours += $otStart->diffInMinutes($effectiveEnd) / 60;
                }
            }
        }

        return [
            'present_days'      => $presentDays,
            'late_count'        => $lateCount,
            'late_minutes_total' => $lateMinutesTotal,
            'overtime_hours'    => round($overtimeHours, 2),
        ];
    }

    private function calculateProration(User $user, Carbon $startDate, Carbon $endDate, int $workingDays): array
    {
        if ($workingDays === 0) {
            return [0, 'Tidak ada hari kerja'];
        }

        $settings = SystemSetting::first();
        $tanggalMasuk = $user->tanggal_masuk ? Carbon::parse($user->tanggal_masuk) : null;
        $tanggalKeluar = $user->tanggal_keluar ? Carbon::parse($user->tanggal_keluar) : null;

        $effectiveStart = $startDate->copy();
        $effectiveEnd = $endDate->copy();
        $reason = null;

        if ($tanggalMasuk && $tanggalMasuk->between($startDate, $endDate)) {
            $effectiveStart = $tanggalMasuk->copy();
            $reason = 'Karyawan baru masuk ' . $tanggalMasuk->format('d/m/Y');
        }

        if ($tanggalKeluar && $tanggalKeluar->between($startDate, $endDate)) {
            $effectiveEnd = $tanggalKeluar->copy();
            $reason = ($reason ? $reason . ', ' : '') . 'Keluar ' . $tanggalKeluar->format('d/m/Y');
        }

        if ($reason === null) {
            return [1.0, null];
        }

        $effectiveWorkingDays = $this->countWorkingDays($effectiveStart, $effectiveEnd, $settings);
        $factor = round($effectiveWorkingDays / $workingDays, 4);

        return [$factor, $reason];
    }

    private function calculateUnpaidLeaveDeduction(
        User $user,
        Carbon $startDate,
        Carbon $endDate,
        float $gajiPokok,
        int $workingDays
    ): float {
        if ($workingDays === 0) {
            return 0;
        }

        $unpaidLeaveTypeIds = LeaveType::where('is_paid', false)
            ->where('is_active', true)
            ->pluck('id');

        if ($unpaidLeaveTypeIds->isEmpty()) {
            return 0;
        }

        $unpaidLeaveDays = Izin::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereIn('jenis', $unpaidLeaveTypeIds)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                  ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
            })
            ->get()
            ->sum(function ($izin) use ($startDate, $endDate) {
                $start = Carbon::parse($izin->tanggal_mulai)->max($startDate);
                $end = Carbon::parse($izin->tanggal_selesai)->min($endDate);
                return max(0, $start->diffInDays($end) + 1);
            });

        if ($unpaidLeaveDays <= 0) {
            return 0;
        }

        return round(($gajiPokok / $workingDays) * $unpaidLeaveDays);
    }
}
