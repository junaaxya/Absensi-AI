<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FaceRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'photos' => 'required|array|min:1|max:6',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail($request->user_id);
        $flaskUrl = config('services.flask.url', env('FLASK_INTERNAL_URL', env('FLASK_SERVICE_URL', 'http://face-service:5000')));

        $savedPaths = [];

        try {
            // 1. Simpan foto sementara & siapkan multipart request
            $pendingRequest = Http::asMultipart();

            foreach ($request->file('photos') as $index => $photo) {
                $filename = "{$user->username}_" . time() . "_{$index}." . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('temp_faces', $filename, 'local');
                $savedPaths[] = $path;
                $absolutePath = Storage::disk('local')->path($path);

                // Attach setiap foto ke request
                $pendingRequest->attach(
                    'photos',
                    fopen($absolutePath, 'r'),
                    $filename
                );
            }

            // 2. Kirim ke Flask Service (single request dengan semua foto + username)
            $response = $pendingRequest->post("{$flaskUrl}/register-face", [
                'username' => $user->username,
            ]);

            if ($response->successful()) {
                // 3. Update status di database
                $user->update(['has_face_data' => true]);

                return response()->json([
                    'message' => 'Data wajah berhasil didaftarkan',
                    'data' => $response->json()
                ]);
            } else {
                Log::error("Flask Error: " . $response->body());
                return response()->json([
                    'message' => 'Gagal memproses di layanan wajah',
                    'error' => $response->body()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error("Face Registration Error: " . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        } finally {
            // 4. Cleanup temp files
            foreach ($savedPaths as $path) {
                Storage::delete($path);
            }
        }
    }
}
