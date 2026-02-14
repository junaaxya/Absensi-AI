<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class AttendanceController extends Controller
{
    /**
     * Calculate distance between two coordinates using Haversine formula.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function autoAttendance(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'type'  => 'required|in:masuk,pulang',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // =========================
        // GEOFENCING VALIDATION
        // =========================
        $settings = SystemSetting::first();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'System settings not configured'
            ], 500);
        }

        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $settings->office_latitude,
            $settings->office_longitude
        );

        if ($distance > $settings->office_radius) {
            return response()->json([
                'success' => false,
                'message' => sprintf(
                    'Out of office range. Distance: %.2f km, Max: %.2f km',
                    $distance,
                    $settings->office_radius
                )
            ], 422);
        }

        // =========================
        // AI FACE RECOGNITION
        // =========================
        try {
            $flaskUrl = env('FLASK_INTERNAL_URL', 'http://face-service:5000') . '/recognize_frame';
            
            $photoFile = $request->file('photo');
            
            $aiResponse = Http::timeout(30)
                ->attach('frame', file_get_contents($photoFile->getRealPath()), $photoFile->getClientOriginalName())
                ->post($flaskUrl);

            if ($aiResponse->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face Service Unavailable'
                ], 500);
            }

            $aiData = $aiResponse->json();

            // Check if face recognition was accepted
            if (!isset($aiData['status']) || $aiData['status'] !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Face not recognized (Low similarity score).'
                ], 422);
            }

            // Find user by username (NIP)
            $recognizedName = $aiData['name'] ?? null;
            $similarityScore = $aiData['score'] ?? 0;

            if (!$recognizedName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face not recognized (Low similarity score).'
                ], 422);
            }

            $user = User::where('username', $recognizedName)->first();

            Log::info("AI Recognition Result", [
                'recognized_name' => $recognizedName,
                'score' => $similarityScore,
                'user_found' => $user ? 'YES' : 'NO'
            ]);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "Face recognized as {$recognizedName}, but user not found in database."
                ], 422);
            }

        } catch (ConnectionException $e) {
            Log::error('Face Service Connection Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Face Service Unavailable'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Face Recognition Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Face Service Unavailable'
            ], 500);
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
                    'lat_in'    => $request->latitude,
                    'long_in'   => $request->longitude,
                    'similarity_score_in' => $similarityScore,
                ]
            );

            Log::info("ABSEN MASUK OK", [
                'user_id'     => $user->id,
                'nama'        => $user->name,
                'tanggal'     => $today,
                'jam_masuk'   => $jamMasuk,
                'statusMasuk' => $statusMasuk,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
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
                    'lat_in'       => $attendance->lat_in,
                    'long_in'      => $attendance->long_in,
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
            'lat_out'    => $request->latitude,
            'long_out'   => $request->longitude,
            'similarity_score_out' => $similarityScore,

            // ⬅️ SIMPAN STATUS PULANG & HADIR DI kegiatan
            'kegiatan'   => $isLembur ? 'hadir_lembur' : 'hadir',
        ]);

        Log::info("ABSEN PULANG OK", [
            'user_id'    => $user->id,
            'nama'       => $user->name,
            'tanggal'    => $today,
            'jam_keluar' => $jamKeluar,
            'kegiatan'   => $attendance->kegiatan,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
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
                'lat_out'       => $attendance->lat_out,
                'long_out'      => $attendance->long_out,
            ]
        ]);
    }
}
