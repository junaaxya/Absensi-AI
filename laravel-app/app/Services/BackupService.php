<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BackupService
{
    private string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function createBackup(): string
    {
        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql.gz';
        $filepath = $this->backupDir . '/' . $filename;

        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s | gzip > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            throw new RuntimeException('Backup database gagal. Return code: ' . $returnCode);
        }

        return $filename;
    }

    /**
     * @return array<int, array{filename: string, size: int, size_human: string, date: string}>
     */
    public function listBackups(): array
    {
        $files = glob($this->backupDir . '/backup-*.sql.gz');

        if (!$files) {
            return [];
        }

        $backups = [];
        foreach ($files as $file) {
            $size = filesize($file);
            $backups[] = [
                'filename' => basename($file),
                'size' => $size,
                'size_human' => $this->formatBytes($size),
                'date' => Carbon::createFromTimestamp(filemtime($file))->format('Y-m-d H:i:s'),
            ];
        }

        usort($backups, fn($a, $b) => strcmp($b['date'], $a['date']));

        return $backups;
    }

    public function deleteBackup(string $filename): bool
    {
        $this->validateFilename($filename);
        $filepath = $this->backupDir . '/' . $filename;

        if (!file_exists($filepath)) {
            return false;
        }

        return unlink($filepath);
    }

    public function cleanupOldBackups(int $days): int
    {
        $cutoff = now()->subDays($days);
        $deleted = 0;

        $files = glob($this->backupDir . '/backup-*.sql.gz');
        if (!$files) {
            return 0;
        }

        foreach ($files as $file) {
            if (Carbon::createFromTimestamp(filemtime($file))->lt($cutoff)) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    public function getBackupPath(string $filename): ?string
    {
        $this->validateFilename($filename);
        $filepath = $this->backupDir . '/' . $filename;

        return file_exists($filepath) ? $filepath : null;
    }

    private function validateFilename(string $filename): void
    {
        if (preg_match('/[\/\\\\]/', $filename) || str_contains($filename, '..')) {
            throw new RuntimeException('Invalid filename.');
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
