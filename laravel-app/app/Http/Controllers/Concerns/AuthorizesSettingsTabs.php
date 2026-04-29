<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait AuthorizesSettingsTabs
{
    protected function categoryTabs(): array
    {
        return [
            "umum" => ["jam_kerja", "lokasi", "profil_perusahaan"],
            "kehadiran" => ["kebijakan_absensi", "shift_kerja", "hari_libur", "tipe_cuti", "poin_pelanggaran", "anti_cheat"],
            "organisasi" => ["departemen"],
            "data" => ["face_recognition", "notifikasi", "export", "backup"],
        ];
    }

    protected function roleAllowedTabs($user): array
    {
        if (!$user) return [];
        
        $allowed = [];
        if ($user->can("manage_system_settings")) {
            $allowed = array_merge($allowed, ["jam_kerja", "lokasi", "profil_perusahaan", "kebijakan_absensi", "face_recognition", "notifikasi", "poin_pelanggaran", "anti_cheat"]);
        }
        if ($user->can("manage_departments")) { $allowed[] = "departemen"; }
        if ($user->can("manage_shifts")) { $allowed[] = "shift_kerja"; }
        if ($user->can("manage_holidays")) { $allowed[] = "hari_libur"; }
        if ($user->can("manage_leave_types")) { $allowed[] = "tipe_cuti"; }
        if ($user->can("export_data")) { $allowed[] = "export"; }
        if ($user->can("manage_backups")) { $allowed[] = "backup"; }
        
        return $allowed;
    }

    protected function authorizeTabAccess(Request $request, string $tab): void
    {
        $allowedTabs = $this->roleAllowedTabs($request->user());

        if (! in_array($tab, $allowedTabs, true)) {
            abort(403);
        }
    }
}
