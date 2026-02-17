<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait AuthorizesSettingsTabs
{
    protected function categoryTabs(): array
    {
        return [
            'umum' => ['jam_kerja', 'lokasi', 'profil_perusahaan'],
            'kehadiran' => ['kebijakan_absensi', 'shift_kerja', 'hari_libur', 'tipe_cuti'],
            'organisasi' => ['departemen'],
            'data' => ['face_recognition', 'notifikasi', 'export', 'backup'],
        ];
    }

    protected function roleAllowedTabs(string $role): array
    {
        $allTabs = array_values(array_unique(array_merge(...array_values($this->categoryTabs()))));

        $roleTabMatrix = [
            'admin' => $allTabs,
            'manager' => ['jam_kerja', 'lokasi', 'kebijakan_absensi', 'notifikasi', 'export'],
            'staf' => ['jam_kerja', 'lokasi', 'notifikasi'],
            'karyawan' => [],
        ];

        return $roleTabMatrix[$role] ?? [];
    }

    protected function authorizeTabAccess(Request $request, string $tab): void
    {
        $userRole = (string) ($request->user()?->role ?? '');
        $allowedTabs = $this->roleAllowedTabs($userRole);

        if (! in_array($tab, $allowedTabs, true)) {
            abort(403);
        }
    }
}
