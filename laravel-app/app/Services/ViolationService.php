<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationType;
use App\Models\WarningLetter;
use Carbon\Carbon;

class ViolationService
{
    public function generateFromAttendance(Attendance $attendance): ?Violation
    {
        if ($attendance->status !== 'terlambat') {
            return null;
        }

        if (Violation::where('reference_type', Attendance::class)
            ->where('reference_id', $attendance->id)->exists()) {
            return null;
        }

        $settings = SystemSetting::first();
        $shift = $attendance->user->shift;
        $workStartTime = $shift ? $shift->start_time : ($settings->work_start_time ?? '08:00:00');
        $lateTolerance = $settings->late_tolerance_minutes ?? 15;

        $batasMasuk = Carbon::createFromFormat('H:i:s', $workStartTime)
            ->addMinutes($lateTolerance);
        $jamMasuk = Carbon::createFromFormat('H:i:s', $attendance->jam_masuk->format('H:i:s'));

        $lateMinutes = $jamMasuk->diffInMinutes($batasMasuk);

        $code = match (true) {
            $lateMinutes <= 15 => 'TELAT_1',
            $lateMinutes <= 30 => 'TELAT_2',
            $lateMinutes <= 60 => 'TELAT_3',
            default => 'TELAT_4',
        };

        $violationType = ViolationType::where('code', $code)->where('is_active', true)->first();
        if (!$violationType) {
            return null;
        }

        return Violation::create([
            'user_id' => $attendance->user_id,
            'violation_type_id' => $violationType->id,
            'tanggal' => $attendance->tanggal,
            'points' => $violationType->points,
            'reference_type' => Attendance::class,
            'reference_id' => $attendance->id,
            'notes' => "Terlambat {$lateMinutes} menit",
        ]);
    }

    public function generateAlpha(User $user, Carbon $date): ?Violation
    {
        $violationType = ViolationType::where('code', 'ALPHA')->where('is_active', true)->first();
        if (!$violationType) {
            return null;
        }

        if (Violation::where('user_id', $user->id)
            ->where('violation_type_id', $violationType->id)
            ->where('tanggal', $date->toDateString())->exists()) {
            return null;
        }

        return Violation::create([
            'user_id' => $user->id,
            'violation_type_id' => $violationType->id,
            'tanggal' => $date->toDateString(),
            'points' => $violationType->points,
            'notes' => 'Tidak hadir tanpa keterangan',
        ]);
    }

    public function getMonthlyPoints(User $user, string $yearMonth): int
    {
        return (int) Violation::where('user_id', $user->id)
            ->where('tanggal', 'like', $yearMonth . '%')
            ->sum('points');
    }

    public function checkWarningThreshold(User $user, string $yearMonth): ?WarningLetter
    {
        $settings = SystemSetting::first();
        $totalPoints = $this->getMonthlyPoints($user, $yearMonth);

        $sp1Threshold = $settings->sp1_threshold ?? 10;
        $sp2Threshold = $settings->sp2_threshold ?? 20;
        $sp3Threshold = $settings->sp3_threshold ?? 30;

        $spType = null;
        if ($totalPoints >= $sp3Threshold) {
            $spType = 'SP3';
        } elseif ($totalPoints >= $sp2Threshold) {
            $spType = 'SP2';
        } elseif ($totalPoints >= $sp1Threshold) {
            $spType = 'SP1';
        }

        if (!$spType) {
            return null;
        }

        if (WarningLetter::where('user_id', $user->id)
            ->where('type', $spType)
            ->where('period_month', $yearMonth)->exists()) {
            return null;
        }

        return WarningLetter::create([
            'user_id' => $user->id,
            'type' => $spType,
            'period_month' => $yearMonth,
            'total_points' => $totalPoints,
            'issued_at' => now(),
        ]);
    }

    public function getPayrollDeduction(User $user, string $yearMonth): float
    {
        $settings = SystemSetting::first();
        $totalPoints = $this->getMonthlyPoints($user, $yearMonth);

        if ($totalPoints === 0) {
            return 0;
        }

        $deductionType = $settings->violation_deduction_type ?? 'per_point';

        if ($deductionType === 'per_point') {
            return $totalPoints * ($settings->violation_deduction_per_point ?? 25000);
        }

        return $user->gaji_pokok * ($settings->violation_deduction_percentage ?? 0) / 100;
    }
}
