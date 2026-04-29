<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;

class LeaveService
{
    private const ROLE_HIERARCHY = [
        'Staf'           => ['Team Leader', 'Manager'],
        'Magang'         => ['Team Leader', 'Manager'],
        'Team Leader'    => ['Manager', 'Vice President'],
        'Supervisor'     => ['Manager', 'Vice President'],
        'Manager'        => ['Vice President', 'Direktur'],
        'Vice President' => ['Direktur'],
    ];

    private const APPROVAL_FLOW = [
        'pending'        => 'approved_l1',
        'approved_l1'    => 'approved_l2',
        'approved_l2'    => 'approved_final',
    ];

    private const MAX_CARRY_OVER = 5;

    public function getBalance(User $user, LeaveType $leaveType, int $year): LeaveBalance
    {
        return LeaveBalance::firstOrCreate(
            [
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
            ],
            [
                'quota' => $leaveType->days_quota,
                'used' => 0,
                'remaining' => $leaveType->days_quota,
                'carry_over' => 0,
            ]
        );
    }

    public function deductBalance(User $user, LeaveType $leaveType, int $days): void
    {
        $balance = $this->getBalance($user, $leaveType, now()->year);
        $balance->update([
            'used' => $balance->used + $days,
            'remaining' => $balance->remaining - $days,
        ]);
    }

    public function restoreBalance(User $user, LeaveType $leaveType, int $days): void
    {
        $balance = $this->getBalance($user, $leaveType, now()->year);
        $balance->update([
            'used' => max(0, $balance->used - $days),
            'remaining' => $balance->remaining + $days,
        ]);
    }

    public function initializeYearlyBalances(int $year): void
    {
        $activeLeaveTypes = LeaveType::active()->get();
        $activeEmployees = User::whereNull('tanggal_keluar')
            ->orWhere('tanggal_keluar', '>', now())
            ->get();

        foreach ($activeEmployees as $employee) {
            foreach ($activeLeaveTypes as $leaveType) {
                LeaveBalance::firstOrCreate(
                    [
                        'user_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $year,
                    ],
                    [
                        'quota' => $leaveType->days_quota,
                        'used' => 0,
                        'remaining' => $leaveType->days_quota,
                        'carry_over' => 0,
                    ]
                );
            }
        }
    }

    public function carryOver(int $year): void
    {
        $previousYear = $year - 1;
        $balances = LeaveBalance::where('year', $previousYear)
            ->where('remaining', '>', 0)
            ->get();

        foreach ($balances as $oldBalance) {
            $carryDays = min($oldBalance->remaining, self::MAX_CARRY_OVER);

            $newBalance = $this->getBalance(
                User::find($oldBalance->user_id),
                LeaveType::find($oldBalance->leave_type_id),
                $year
            );

            $newBalance->update([
                'carry_over' => $carryDays,
                'quota' => $newBalance->quota + $carryDays,
                'remaining' => $newBalance->remaining + $carryDays,
            ]);
        }
    }

    public function getNextApprover(Izin $izin): ?User
    {
        $submitter = $izin->user;
        $submitterRole = $submitter->getRoleNames()->first();

        if (!isset(self::ROLE_HIERARCHY[$submitterRole])) {
            return null;
        }

        $approverChain = self::ROLE_HIERARCHY[$submitterRole];
        $currentStatus = $izin->approval_status;

        $approverIndex = match ($currentStatus) {
            'pending' => 0,
            'approved_l1' => 1,
            default => null,
        };

        if ($approverIndex === null || $approverIndex >= count($approverChain)) {
            return null;
        }

        $targetRole = $approverChain[$approverIndex];

        $approver = User::role($targetRole)
            ->where('department_id', $submitter->department_id)
            ->where('id', '!=', $submitter->id)
            ->first();

        if (!$approver) {
            $approver = User::role($targetRole)
                ->where('id', '!=', $submitter->id)
                ->first();
        }

        return $approver;
    }

    public function approve(Izin $izin, User $approver): void
    {
        $currentStatus = $izin->approval_status;
        $submitterRole = $izin->user->getRoleNames()->first();
        $approverChain = self::ROLE_HIERARCHY[$submitterRole] ?? [];

        $approvalLog = $izin->approved_by ?? [];
        $approvalLog[] = [
            'user_id' => $approver->id,
            'role' => $approver->getRoleNames()->first(),
            'action' => 'approved',
            'timestamp' => now()->toIso8601String(),
        ];

        $nextStatus = self::APPROVAL_FLOW[$currentStatus] ?? 'approved_final';

        $isLastLevel = match ($currentStatus) {
            'pending' => count($approverChain) <= 1,
            'approved_l1' => count($approverChain) <= 2,
            default => true,
        };

        if ($isLastLevel) {
            $nextStatus = 'approved_final';
        }

        $nextApprover = null;
        if ($nextStatus !== 'approved_final') {
            $izin->approval_status = $nextStatus;
            $nextApprover = $this->getNextApprover($izin);
            $izin->approval_status = $currentStatus;

            if (!$nextApprover) {
                $nextStatus = 'approved_final';
            }
        }

        $updateData = [
            'approval_status' => $nextStatus,
            'approved_by' => $approvalLog,
            'current_approver_id' => $nextApprover?->id,
        ];

        if ($nextStatus === 'approved_final') {
            $updateData['status'] = 'approved';
            $updateData['current_approver_id'] = null;

            if ($izin->leave_type_id && $izin->leaveType) {
                $days = $izin->tanggal_mulai->diffInDays($izin->tanggal_selesai) + 1;
                $this->deductBalance($izin->user, $izin->leaveType, $days);
            }
        }

        $izin->update($updateData);
    }

    public function reject(Izin $izin, User $approver, string $reason = ''): void
    {
        $approvalLog = $izin->approved_by ?? [];
        $approvalLog[] = [
            'user_id' => $approver->id,
            'role' => $approver->getRoleNames()->first(),
            'action' => 'rejected',
            'reason' => $reason,
            'timestamp' => now()->toIso8601String(),
        ];

        $izin->update([
            'approval_status' => 'rejected',
            'status' => 'rejected',
            'approved_by' => $approvalLog,
            'current_approver_id' => null,
        ]);
    }
}
