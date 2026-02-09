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
        $flaskUrl = config('services.flask.url', env('FLASK_SERVICE_URL', 'http://flask:5000'));
        
        $savedPaths = [];
        $flaskPhotos = [];

        try {
            // 1. Simpan foto sementara di Laravel Storage
            foreach ($request->file('photos') as $index => $photo) {
                // Generate unique filename: userid_timestamp_index.jpg
                $filename = "{$user->id}_" . time() . "_{$index}." . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('temp_faces', $filename, 'local');
                
                $savedPaths[] = $path;
                
                // Siapkan untuk dikirim ke Flask
                $flaskPhotos[] = [
                    'name' => 'photos',
                    'contents' => fopen(storage_path("app/{$path}"), 'r'),
                    'filename' => $filename
                ];
            }

            // 2. Kirim ke Flask Service untuk generate embedding
            // Note: Flask service perlu endpoint /register yang menerima multipart/form-data
            $response = Http::post("{$flaskUrl}/register-face", [
                'user_id' => $user->id,
            ]);
            
            // Karena Http client Laravel agak tricky dengan multiple files dengan key sama, 
            // kita gunakan pendekatan manual jika diperlukan, atau loop attach.
            // Pendekatan attach manual:
            $pendingRequest = Http::asMultipart();
            foreach ($flaskPhotos as $photo) {
                $pendingRequest->attach($photo['name'], $photo['contents'], $photo['filename']);
            }
            // Kirim user_id sebagai field biasa
            $response = $pendingRequest->post("{$flaskUrl}/register-face", [
                'user_id' => $user->id
            ]);

            if ($response->successful()) {
                // 3. Update status di database (jika sukses)
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
