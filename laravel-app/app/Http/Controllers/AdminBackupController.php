<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesSettingsTabs;
use App\Services\BackupService;
use Illuminate\Http\Request;

class AdminBackupController extends Controller
{
    use AuthorizesSettingsTabs;

    public function store(Request $request, BackupService $backupService)
    {
        $this->authorizeTabAccess($request, 'backup');

        try {
            $filename = $backupService->createBackup();
            return back()->with('success', "Backup berhasil dibuat: {$filename}");
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, string $filename, BackupService $backupService)
    {
        $this->authorizeTabAccess($request, 'backup');

        if ($backupService->deleteBackup($filename)) {
            return back()->with('success', 'Backup berhasil dihapus.');
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }

    public function download(Request $request, string $filename, BackupService $backupService)
    {
        $this->authorizeTabAccess($request, 'backup');

        $filepath = $backupService->getBackupPath($filename);

        if (!$filepath) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/gzip',
        ]);
    }

    public function cleanup(Request $request)
    {
        $this->authorizeTabAccess($request, 'backup');

        try {
            \Illuminate\Support\Facades\Artisan::call('cleanup:old-data');
            return back()->with('success', 'Pembersihan data lama berhasil dilakukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan pembersihan: ' . $e->getMessage());
        }
    }
}
