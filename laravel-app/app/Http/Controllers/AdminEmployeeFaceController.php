<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminEmployeeFaceController extends Controller
{
    private function flaskBaseUrls(): array
    {
        $urls = [
            config('services.flask.url'),
            env('FLASK_INTERNAL_URL'),
            env('FLASK_SERVICE_URL'),
            'http://face-service:5000',
            'http://localhost:5000',
        ];

        $normalized = [];
        foreach ($urls as $url) {
            if (!is_string($url) || trim($url) === '') {
                continue;
            }

            $normalized[] = rtrim($url, '/');
        }

        return array_values(array_unique($normalized));
    }

    private function requestFaceService(string $method, string $path): array
    {
        $lastError = null;

        foreach ($this->flaskBaseUrls() as $baseUrl) {
            try {
                $response = Http::timeout(30)->send($method, "{$baseUrl}{$path}");

                return [
                    'ok' => true,
                    'base_url' => $baseUrl,
                    'response' => $response,
                ];
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        return [
            'ok' => false,
            'error' => $lastError ?? 'Face service is unreachable.',
        ];
    }

    public function show(User $employee)
    {
        try {
            $requestResult = $this->requestFaceService('GET', "/face-dataset/{$employee->username}");

            if (!$requestResult['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data wajah dari face service.',
                    'error' => $requestResult['error'],
                ], 500);
            }

            $baseUrl = $requestResult['base_url'];
            $response = $requestResult['response'];

            if ($response->failed()) {
                if ($response->status() === 404) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Endpoint dataset wajah belum tersedia, menampilkan data kosong.',
                        'data' => [
                            'username' => $employee->username,
                            'registered' => (bool) $employee->has_face_data,
                            'photo_count' => 0,
                            'photos' => [],
                        ],
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data wajah dari face service.',
                    'error' => $response->json('message') ?? $response->body(),
                ], 500);
            }

            $payload = $response->json();
            $photos = collect($payload['photos'] ?? [])->map(function ($photo) use ($baseUrl) {
                $relativePath = $photo['photo_url'] ?? null;
                $photoUrl = null;

                if (is_string($relativePath) && str_starts_with($relativePath, '/')) {
                    $photoUrl = $baseUrl . $relativePath;
                }

                return [
                    'filename' => $photo['filename'] ?? null,
                    'status' => $photo['status'] ?? 'valid',
                    'photo_url' => $photoUrl,
                ];
            })->filter(fn ($photo) => !empty($photo['filename']))->values();

            return response()->json([
                'success' => true,
                'message' => 'Data wajah berhasil diambil.',
                'data' => [
                    'username' => $employee->username,
                    'registered' => (bool) ($payload['registered'] ?? $employee->has_face_data),
                    'photo_count' => (int) ($payload['photo_count'] ?? $photos->count()),
                    'photos' => $photos,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Admin face data fetch failed', [
                'employee_id' => $employee->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data wajah.',
            ], 500);
        }
    }

    public function destroy(User $employee)
    {
        try {
            $requestResult = $this->requestFaceService('DELETE', "/face-dataset/{$employee->username}");

            if (!$requestResult['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus dataset wajah.',
                    'error' => $requestResult['error'],
                ], 500);
            }

            $response = $requestResult['response'];

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus dataset wajah.',
                    'error' => $response->json('message') ?? $response->body(),
                ], 500);
            }

            $employee->update(['has_face_data' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Dataset wajah berhasil dihapus.',
                'data' => [
                    'photo_count' => 0,
                    'registered' => false,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Admin face dataset delete failed', [
                'employee_id' => $employee->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus dataset wajah.',
            ], 500);
        }
    }

    public function destroyPhoto(User $employee, string $photo)
    {
        try {
            $safePhoto = rawurlencode($photo);
            $requestResult = $this->requestFaceService('DELETE', "/face-dataset/{$employee->username}/photo/{$safePhoto}");

            if (!$requestResult['ok']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus foto wajah.',
                    'error' => $requestResult['error'],
                ], 500);
            }

            $response = $requestResult['response'];

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus foto wajah.',
                    'error' => $response->json('message') ?? $response->body(),
                ], 500);
            }

            $payload = $response->json();
            $remaining = (int) ($payload['remaining_photo_count'] ?? 0);
            $registered = $remaining > 0;

            $employee->update(['has_face_data' => $registered]);

            return response()->json([
                'success' => true,
                'message' => 'Foto wajah berhasil dihapus.',
                'data' => [
                    'photo_count' => $remaining,
                    'registered' => $registered,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Admin face photo delete failed', [
                'employee_id' => $employee->id,
                'photo' => $photo,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus foto wajah.',
            ], 500);
        }
    }
}
