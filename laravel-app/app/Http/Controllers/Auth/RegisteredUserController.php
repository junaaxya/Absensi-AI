<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'foto' => ['nullable', 'image', 'max:2048'],
            'face_photos.*' => ['nullable', 'image', 'max:2048'],
            'base64_faces' => ['nullable', 'array'],
        ]);

        DB::beginTransaction();

        try {
            // Generate username dari nama (lowercase, tanpa spasi) — inside transaction to prevent race condition
            $username = strtolower(str_replace(' ', '', $request->name));

            // Pastikan username unik (within transaction scope)
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $username,
                'password' => Hash::make($request->password),
                'foto' => $fotoPath,
                'is_approved' => false,
            ]);

            $user->assignRole('Staf');

            $flaskUrl = config('services.flask.url', env('FLASK_INTERNAL_URL', env('FLASK_SERVICE_URL', 'http://face-service:5000')));
            $savedPaths = [];
            $pendingRequest = Http::asMultipart();
            $hasFaces = false;

            // Handle Base64 from Camera
            if ($request->has('base64_faces') && is_array($request->base64_faces)) {
                foreach ($request->base64_faces as $index => $base64) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                        $data = substr($base64, strpos($base64, ',') + 1);
                        $data = base64_decode($data);
                        $filename = "{$user->username}_" . time() . "_{$index}.jpg";
                        Storage::disk('local')->put("temp_faces/{$filename}", $data);
                        $savedPaths[] = "temp_faces/{$filename}";
                        $absolutePath = Storage::disk('local')->path("temp_faces/{$filename}");
                        $pendingRequest->attach('photos', fopen($absolutePath, 'r'), $filename);
                        $hasFaces = true;
                    }
                }
            }

            // Handle File Uploads
            if ($request->hasFile('face_photos')) {
                foreach ($request->file('face_photos') as $index => $photo) {
                    $filename = "{$user->username}_" . time() . "_file_{$index}." . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('temp_faces', $filename, 'local');
                    $savedPaths[] = $path;
                    $absolutePath = Storage::disk('local')->path($path);
                    $pendingRequest->attach('photos', fopen($absolutePath, 'r'), $filename);
                    $hasFaces = true;
                }
            }

            // Send to Flask
            if ($hasFaces) {
                try {
                    $response = $pendingRequest->post("{$flaskUrl}/register-face", [
                        'username' => $user->username,
                    ]);

                    if ($response->successful()) {
                        $user->update(['has_face_data' => true]);
                    } else {
                        Log::error("Flask Error: " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("Face Registration Error: " . $e->getMessage());
                } finally {
                    foreach ($savedPaths as $path) {
                        Storage::delete($path);
                    }
                }
            }

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            return redirect(route('dashboard', absolute: false))
                ->with('success', 'Selamat datang, ' . $user->name . '! Akun Anda berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration error: " . $e->getMessage());
            return back()->withInput()->withErrors(['foto' => 'Registration failed: ' . $e->getMessage()]);
        }
}
}
