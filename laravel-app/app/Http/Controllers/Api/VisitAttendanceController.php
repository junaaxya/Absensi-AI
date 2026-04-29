<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitAttendance;
use App\Models\VisitLocation;
use App\Models\SystemSetting;
use App\Models\EmployeeDevice;
use App\Services\AntiCheatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class VisitAttendanceController extends Controller
{
    /**
     * Check in for a visit attendance.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric|min:0',
            'client_name' => 'required|string|max:255',
            'location_name' => 'required|string|max:255',
            'purpose' => 'required|string|max:2000',
            'device_fingerprint' => 'nullable|string|max:255',
            'gps_readings' => 'nullable|string',
            'timezone_client' => 'nullable|string|max:100',
        ]);

        $authUser = auth()->user();
        $settings = SystemSetting::first();

        // Anti-cheat validation (reuse from regular attendance)
        $gpsReadings = json_decode($request->input('gps_readings', '[]'), true) ?: [];
        $deviceFingerprint = $request->input('device_fingerprint');
        $timezoneClient = $request->input('timezone_client');

        $antiCheatResult = null;

        if ($settings && $settings->enable_anti_cheat && $authUser) {
            $antiCheatService = new AntiCheatService([
                'gps_readings' => $gpsReadings,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->input('accuracy'),
                'device_fingerprint' => $deviceFingerprint,
                'timezone_client' => $timezoneClient,
                'mock_location_detected' => false,
            ], $authUser, $settings);

            $antiCheatResult = $antiCheatService->validate();

            if (!$antiCheatResult->passed) {
                Log::warning('Anti-cheat rejected visit check-in', [
                    'user_id' => $authUser->id,
                    'score' => $antiCheatResult->score,
                    'flags' => $antiCheatResult->flags,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Terdeteksi anomali lokasi. Hubungi admin.',
                ], 403);
            }
        }

        // Register/update device
        if ($deviceFingerprint && $authUser) {
            EmployeeDevice::updateOrCreate(
                ['device_fingerprint' => $deviceFingerprint, 'user_id' => $authUser->id],
                [
                    'device_name' => $request->userAgent(),
                    'platform' => php_uname('s'),
                    'browser' => $request->header('User-Agent'),
                    'screen_resolution' => null,
                    'last_used_at' => now(),
                ]
            );
        }

        // Face recognition via Flask
        try {
            $flaskUrl = env('FLASK_INTERNAL_URL', 'http://face-service:5000') . '/recognize_frame';

            $photoFile = $request->file('photo');

            $aiResponse = Http::timeout(30)
                ->attach('frame', file_get_contents($photoFile->getRealPath()), $photoFile->getClientOriginalName())
                ->post($flaskUrl);

            if ($aiResponse->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face Service Unavailable',
                ], 500);
            }

            $aiData = $aiResponse->json();

            if (!isset($aiData['status']) || $aiData['status'] !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak dikenali (skor kesamaan rendah).',
                ], 422);
            }

            $recognizedName = $aiData['name'] ?? null;
            $similarityScore = $aiData['score'] ?? 0;

            if (!$recognizedName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak dikenali (skor kesamaan rendah).',
                ], 422);
            }

            // Verify face matches authenticated user
            if ($authUser && $recognizedName !== $authUser->username) {
                Log::warning('Visit face identity mismatch', [
                    'auth_user' => $authUser->username,
                    'recognized_as' => $recognizedName,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak sesuai dengan akun Anda.',
                ], 403);
            }
        } catch (ConnectionException $e) {
            Log::error('Face Service Connection Error (Visit)', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Face Service Unavailable',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Face Recognition Error (Visit)', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Face Service Unavailable',
            ], 500);
        }

        // Store photo
        $photoPath = $photoFile->store('visit-photos', 'public');

        $now = Carbon::now();

        $visit = VisitAttendance::create([
            'user_id' => $authUser->id,
            'tanggal' => $now->toDateString(),
            'client_name' => $request->client_name,
            'location_name' => $request->location_name,
            'purpose' => $request->purpose,
            'check_in_time' => $now,
            'check_in_lat' => $request->latitude,
            'check_in_long' => $request->longitude,
            'check_in_photo' => $photoPath,
            'similarity_score_in' => $similarityScore,
            'status' => 'active',
            'device_fingerprint' => $deviceFingerprint,
            'anomaly_score' => $antiCheatResult ? $antiCheatResult->score : 0,
        ]);

        Log::info('Visit check-in OK', [
            'user_id' => $authUser->id,
            'visit_id' => $visit->id,
            'client' => $request->client_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in kunjungan berhasil.',
            'data' => [
                'visit_id' => $visit->id,
                'client_name' => $visit->client_name,
                'location_name' => $visit->location_name,
                'check_in_time' => $visit->check_in_time->format('H:i'),
            ],
        ]);
    }

    /**
     * Check out from a visit attendance.
     */
    public function checkOut(Request $request, VisitAttendance $visitAttendance)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $authUser = auth()->user();

        // Ensure the visit belongs to the authenticated user
        if ($visitAttendance->user_id !== $authUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan ini bukan milik Anda.',
            ], 403);
        }

        if ($visitAttendance->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan ini sudah selesai atau dibatalkan.',
            ], 422);
        }

        // Face recognition via Flask
        try {
            $flaskUrl = env('FLASK_INTERNAL_URL', 'http://face-service:5000') . '/recognize_frame';

            $photoFile = $request->file('photo');

            $aiResponse = Http::timeout(30)
                ->attach('frame', file_get_contents($photoFile->getRealPath()), $photoFile->getClientOriginalName())
                ->post($flaskUrl);

            if ($aiResponse->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face Service Unavailable',
                ], 500);
            }

            $aiData = $aiResponse->json();

            if (!isset($aiData['status']) || $aiData['status'] !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak dikenali (skor kesamaan rendah).',
                ], 422);
            }

            $recognizedName = $aiData['name'] ?? null;
            $similarityScore = $aiData['score'] ?? 0;

            if (!$recognizedName || $recognizedName !== $authUser->username) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wajah tidak sesuai dengan akun Anda.',
                ], 403);
            }
        } catch (ConnectionException $e) {
            Log::error('Face Service Connection Error (Visit Checkout)', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Face Service Unavailable',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Face Recognition Error (Visit Checkout)', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Face Service Unavailable',
            ], 500);
        }

        $photoPath = $photoFile->store('visit-photos', 'public');

        $visitAttendance->update([
            'check_out_time' => Carbon::now(),
            'check_out_lat' => $request->latitude,
            'check_out_long' => $request->longitude,
            'check_out_photo' => $photoPath,
            'similarity_score_out' => $similarityScore,
            'status' => 'completed',
        ]);

        Log::info('Visit check-out OK', [
            'user_id' => $authUser->id,
            'visit_id' => $visitAttendance->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out kunjungan berhasil.',
            'data' => [
                'visit_id' => $visitAttendance->id,
                'check_out_time' => $visitAttendance->check_out_time->format('H:i'),
                'status' => 'completed',
            ],
        ]);
    }

    /**
     * Track GPS location during an active visit.
     */
    public function trackLocation(Request $request, VisitAttendance $visitAttendance)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric|min:0',
        ]);

        $authUser = auth()->user();

        if ($visitAttendance->user_id !== $authUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan ini bukan milik Anda.',
            ], 403);
        }

        if ($visitAttendance->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan ini sudah tidak aktif.',
            ], 422);
        }

        $location = VisitLocation::create([
            'visit_attendance_id' => $visitAttendance->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy,
            'recorded_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi tercatat.',
            'data' => [
                'location_id' => $location->id,
                'recorded_at' => $location->recorded_at->format('H:i:s'),
            ],
        ]);
    }
}
