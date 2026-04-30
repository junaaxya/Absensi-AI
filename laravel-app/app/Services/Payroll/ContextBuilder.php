<?php

namespace App\Services\Payroll;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Izin;
use App\Models\LeaveType;
use App\Models\PayrollPeriod;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ContextBuilder
{
    private const DAY_NAME_TO_NUMBER = [
        'Sunday' => 0,
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6,
    ];

    public function build(User $user, PayrollPeriod $period): array
    {
        $settings = SystemSetting::first();
        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);

        $workingDays = $this->countWorkingDays($startDate, $endDate, $settings);
        $attendance = $this->countAttendance($user, $startDate, $endDate, $settings);
        $leaveData = $this->countLeave($user, $startDate, $endDate);
        $prorateFactor = $this->calculateProrateFactor($user, $startDate, $endDate, $workingDays, $settings);

        return [
            'working_days' => $workingDays,
            'present_days' => $attendance['present_days'],
            'absent_days' => max(0, $workingDays - $attendance['present_days']),
            'late_count' => $attendance['late_count'],
            'overtime_hours' => $attendance['overtime_hours'],
            'leave_days_unpaid' => $leaveData['unpaid_days'],
            'sick_days' => $leaveData['sick_days'],
            'basic_salary' => (float) $user->gaji_pokok,
            'gross_salary' => 0.0,
            'prorate_factor' => $prorateFactor,
            'bpjs_basis_salary' => (float) $user->gaji_pokok,
            'taxable_income' => 0.0,
            'ter_rate' => 0.0,
            'ptkp_status' => 1.0,
            'ptkp_amount' => 54000000.0,
            '_late_minutes_total' => $attendance['late_minutes_total'],
            '_prorate_reason' => $attendance['_prorate_reason'] ?? null,
        ];
    }

    private function countWorkingDays(Carbon $startDate, Carbon $endDate, ?SystemSetting $settings): int
    {
        $weekendDayNames = $settings->weekend_days ?? ['Saturday', 'Sunday'];
        $weekendNumbers = array_filter(
            array_map(fn ($name) => self::DAY_NAME_TO_NUMBER[$name] ?? null, $weekendDayNames),
            fn ($v) => $v !== null
        );

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
            'present_days' => $presentDays,
            'late_count' => $lateCount,
            'late_minutes_total' => $lateMinutesTotal,
            'overtime_hours' => round($overtimeHours, 2),
        ];
    }

    private function countLeave(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $unpaidLeaveTypeIds = LeaveType::where('is_paid', false)
            ->where('is_active', true)
            ->pluck('id');

        $unpaidDays = 0;
        if ($unpaidLeaveTypeIds->isNotEmpty()) {
            $unpaidDays = Izin::where('user_id', $user->id)
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
        }

        $sickDays = Izin::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('jenis', 'sakit')
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

        return [
            'unpaid_days' => $unpaidDays,
            'sick_days' => $sickDays,
        ];
    }

    private function calculateProrateFactor(
        User $user,
        Carbon $startDate,
        Carbon $endDate,
        int $workingDays,
        ?SystemSetting $settings
    ): float {
        if ($workingDays === 0) {
            return 0.0;
        }

        $tanggalMasuk = $user->tanggal_masuk ? Carbon::parse($user->tanggal_masuk) : null;
        $tanggalKeluar = $user->tanggal_keluar ? Carbon::parse($user->tanggal_keluar) : null;

        $effectiveStart = $startDate->copy();
        $effectiveEnd = $endDate->copy();
        $needsProrate = false;

        if ($tanggalMasuk && $tanggalMasuk->between($startDate, $endDate)) {
            $effectiveStart = $tanggalMasuk->copy();
            $needsProrate = true;
        }

        if ($tanggalKeluar && $tanggalKeluar->between($startDate, $endDate)) {
            $effectiveEnd = $tanggalKeluar->copy();
            $needsProrate = true;
        }

        if (!$needsProrate) {
            return 1.0;
        }

        $effectiveWorkingDays = $this->countWorkingDays($effectiveStart, $effectiveEnd, $settings);
        return round($effectiveWorkingDays / $workingDays, 4);
    }
}
