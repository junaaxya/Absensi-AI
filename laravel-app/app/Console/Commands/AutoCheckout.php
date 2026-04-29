<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCheckout extends Command
{
    protected $signature = 'attendance:auto-checkout';

    protected $description = 'Automatically checkout employees who forgot to checkout';

    public function handle(): int
    {
        $settings = SystemSetting::first();
        $autoCheckoutTime = $settings->auto_checkout_time ?? '18:00';

        $today = Carbon::today();
        $checkoutTimestamp = Carbon::parse($today->toDateString() . ' ' . $autoCheckoutTime);

        if (now()->lt($checkoutTimestamp)) {
            $this->info("Auto-checkout time ({$autoCheckoutTime}) has not been reached yet. Skipping.");

            return Command::SUCCESS;
        }

        $pendingAttendances = Attendance::where('tanggal', $today->toDateString())
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->get();

        if ($pendingAttendances->isEmpty()) {
            $this->info('No pending checkouts found. Nothing to do.');

            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($pendingAttendances as $attendance) {
            $oldValues = [
                'jam_keluar' => null,
                'status' => $attendance->status,
            ];

            $attendance->jam_keluar = $checkoutTimestamp;

            $currentStatus = $attendance->status;
            if ($currentStatus && $currentStatus !== 'hadir') {
                $attendance->status = $currentStatus . ',auto_checkout';
            } else {
                $attendance->status = 'auto_checkout';
            }

            // withoutAuditLog: trait can't resolve auth user in console context, so we log manually below
            Attendance::withoutAuditLog(function () use ($attendance) {
                $attendance->save();
            });

            $newValues = [
                'jam_keluar' => $checkoutTimestamp->format('H:i:s'),
                'status' => $attendance->status,
            ];

            AuditLog::create([
                'user_id' => null,
                'auditable_type' => Attendance::class,
                'auditable_id' => $attendance->id,
                'event' => 'updated',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'AutoCheckout Console Command',
                'url' => 'artisan attendance:auto-checkout',
                'created_at' => now(),
            ]);

            $count++;
            $userName = $attendance->user->name ?? ('User #' . $attendance->user_id);
            $this->line("  Auto-checkout: {$userName} (ID: {$attendance->id})");
        }

        $this->info("Auto-checkout completed: {$count} employee(s) checked out at {$autoCheckoutTime}.");

        return Command::SUCCESS;
    }
}
