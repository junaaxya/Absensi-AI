<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function autoAttendance(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'score' => 'required|numeric',
            'type'  => 'required|in:masuk,pulang',
        ]);

        // =========================
        // NORMALISASI NAMA
        // =========================
        $rawName = str_replace('_', ' ', $request->name);

        $user = User::whereRaw(
            'LOWER(name) = ?',
            [strtolower($rawName)]
        )->first();

        Log::info("Nama dari Flask: " . $rawName);
        Log::info("User ditemukan: " . ($user ? $user->name : 'TIDAK ADA'));

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // =========================
        // WAKTU SEKARANG
        // =========================
        $now   = Carbon::now();
        $today = $now->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        // =====================================================
        // ===================== ABSEN MASUK ====================
        // =====================================================
        if ($request->type === 'masuk') {

            if ($attendance && $attendance->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sudah absen masuk hari ini'
                ], 409);
            }

            $jamMasuk = $now->format('H:i:s');

            // batas jam masuk 08:00
            $batasMasuk = Carbon::createFromTime(8, 0, 0);
            $statusMasuk = $now->gt($batasMasuk) ? 'terlambat' : 'tepat_waktu';

            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'tanggal' => $today,
                ],
                [
                    'jam_masuk' => $jamMasuk,
                    'status'    => $statusMasuk, // ⬅️ STATUS MASUK DIKUNCI DI SINI
                    'kegiatan'  => null,         // reset aman
                ]
            );

            Log::info("ABSEN MASUK OK", [
                'user_id'     => $user->id,
                'nama'        => $user->name,
                'tanggal'     => $today,
                'jam_masuk'   => $jamMasuk,
                'statusMasuk' => $statusMasuk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil',
                'data' => [
                    'user_name'    => $user->name,
                    'tanggal'      => $attendance->tanggal,
                    'jam_masuk'    => $attendance->jam_masuk,
                    'status_masuk' => $attendance->status,
                    'jam_keluar'   => $attendance->jam_keluar,
                ]
            ]);
        }

        // =====================================================
        // ===================== ABSEN PULANG ===================
        // =====================================================
        if (!$attendance || !$attendance->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Belum absen masuk hari ini'
            ], 409);
        }

        if ($attendance->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah absen pulang hari ini'
            ], 409);
        }

        $jamKeluar = $now->format('H:i:s');

        // batas jam pulang 17:00
        $batasPulang = Carbon::createFromTime(17, 0, 0);
        $isLembur = $now->gte($batasPulang);

        // ⛔ JANGAN PERNAH sentuh kolom status di sini
        $attendance->update([
            'jam_keluar' => $jamKeluar,

            // ⬅️ SIMPAN STATUS PULANG & HADIR DI kegiatan
            'kegiatan'   => $isLembur ? 'hadir_lembur' : 'hadir',
        ]);

        Log::info("ABSEN PULANG OK", [
            'user_id'    => $user->id,
            'nama'       => $user->name,
            'tanggal'    => $today,
            'jam_keluar' => $jamKeluar,
            'kegiatan'   => $attendance->kegiatan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen pulang berhasil',
            'data' => [
                'user_name'     => $user->name,
                'tanggal'       => $attendance->tanggal,
                'jam_masuk'     => $attendance->jam_masuk,
                'jam_keluar'    => $attendance->jam_keluar,
                'status_masuk'  => $attendance->status,        // TERLAMBAT / TEPAT_WAKTU
                'status_pulang' => $isLembur ? 'lembur' : 'tepat_waktu',
                'status_hadir'  => 'hadir',
                'kegiatan'      => $attendance->kegiatan,      // hadir / hadir_lembur
            ]
        ]);
    }
}
