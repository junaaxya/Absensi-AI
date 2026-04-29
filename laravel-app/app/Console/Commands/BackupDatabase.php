<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Create a database backup (mysqldump)';

    public function handle(BackupService $backupService): int
    {
        $this->info('Starting database backup...');

        try {
            $filename = $backupService->createBackup();
            $this->info("Backup created: {$filename}");
            return Command::SUCCESS;
        } catch (\RuntimeException $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
