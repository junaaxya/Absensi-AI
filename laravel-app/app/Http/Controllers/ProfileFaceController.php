<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProfileFaceController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $flaskUrl = config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000'));

        try {
            $response = Http::timeout(10)->get("{$flaskUrl}/face-dataset/{$user->name}");
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['registered' => false, 'photo_count' => 0, 'photos' => []]);
        } catch (\Exception $e) {
            return response()->json([
                'registered' => false,
                'photo_count' => 0,
                'photos' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        $flaskUrl = config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000'));

        try {
            Http::timeout(10)->delete("{$flaskUrl}/face-dataset/{$user->name}");
            $user->update(['has_face_data' => false]);
            return response()->json(['success' => true, 'message' => 'Data wajah berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
