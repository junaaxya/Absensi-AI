<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Services\BackupService;
use Illuminate\Console\Command;

class CleanupOldData extends Command
{
    protected $signature = 'cleanup:old-data';
    protected $description = 'Delete old audit logs and backup files based on retention settings';

    public function handle(BackupService $backupService): int
    {
        $settings = SystemSetting::first();
        $auditRetentionDays = $settings->audit_log_retention_days ?? 90;
        $backupRetentionDays = $settings->backup_retention_days ?? 30;

        $auditDeleted = AuditLog::where('created_at', '<', now()->subDays($auditRetentionDays))->delete();
        $this->info("Deleted {$auditDeleted} audit log(s) older than {$auditRetentionDays} days.");

        $backupsDeleted = $backupService->cleanupOldBackups($backupRetentionDays);
        $this->info("Deleted {$backupsDeleted} backup file(s) older than {$backupRetentionDays} days.");

        return Command::SUCCESS;
    }
}
