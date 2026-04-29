<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Izin;
use App\Models\User;
use App\Services\ViolationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAlphaViolations extends Command
{
    protected $signature = 'violations:generate-alpha';
    protected $description = 'Generate ALPHA violations for absent employees';

    public function handle(ViolationService $violationService): int
    {
        $today = Carbon::today();

        $activeEmployees = User::whereNotNull('status_karyawan')
            ->whereNull('tanggal_keluar')
            ->get();

        $generated = 0;

        foreach ($activeEmployees as $user) {
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->where('tanggal', $today->toDateString())
                ->whereNotNull('jam_masuk')
                ->exists();

            if ($hasAttendance) {
                continue;
            }

            $hasApprovedIzin = Izin::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('tanggal_mulai', '<=', $today->toDateString())
                ->where('tanggal_selesai', '>=', $today->toDateString())
                ->exists();

            if ($hasApprovedIzin) {
                continue;
            }

            $violation = $violationService->generateAlpha($user, $today);
            if ($violation) {
                $generated++;
                $yearMonth = $today->format('Y-m');
                $violationService->checkWarningThreshold($user, $yearMonth);
            }
        }

        $this->info("Generated {$generated} ALPHA violation(s) for {$today->toDateString()}.");

        return Command::SUCCESS;
    }
}
