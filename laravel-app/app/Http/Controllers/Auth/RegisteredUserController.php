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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Generate username dari nama (lowercase, tanpa spasi)
        $username = strtolower(str_replace(' ', '', $request->name));
        
        // Pastikan username unik
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $username,
                'password' => Hash::make($request->password),
            ]);

            // Send photo to Face Service for registration
            $flaskUrl = config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000'));
            $photo = $request->file('photo');

            $response = Http::attach(
                'file',
                file_get_contents($photo->getRealPath()),
                $photo->getClientOriginalName()
            )->post("{$flaskUrl}/register", [
                'name' => $username,
            ]);

            if (!$response->successful()) {
                DB::rollBack();
                $errorMessage = $response->json('message', 'Face registration failed');
                Log::error("Face registration failed during user registration: " . $errorMessage);
                return back()->withInput()->withErrors(['photo' => 'Face registration failed: ' . $errorMessage]);
            }

            // Update user to indicate face data is registered
            $user->update(['has_face_data' => true]);

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            return redirect(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration error: " . $e->getMessage());
            return back()->withInput()->withErrors(['photo' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}
