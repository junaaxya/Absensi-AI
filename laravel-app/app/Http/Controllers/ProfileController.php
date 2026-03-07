<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();
    $user->fill($request->validated());

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    // Upload foto (sesuai dengan 'foto' di Request)
    if ($request->hasFile('foto')) {

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $foto = $request->file('foto')->store('profile', 'public');
        $user->foto = $foto;
    }

    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}


    /**
     * Serve a user's profile photo with hybrid fallback.
     * Accepts optional user ID so admins can view any employee's photo.
     * Self-caching: fetches from Flask once, saves to Laravel storage permanently.
     */
    public function photo(Request $request, ?int $user = null)
    {
        // Resolve target user: if ID provided and requester is admin, look up that user
        if ($user && $request->user()->role === 'admin') {
            $targetUser = \App\Models\User::find($user);
            if (!$targetUser) {
                return response()->file(public_path('img/default-avatar.svg'));
            }
        } else {
            $targetUser = $request->user();
        }

        // Tier 1: already has an uploaded photo
        if ($targetUser->foto && Storage::disk('public')->exists($targetUser->foto)) {
            return redirect(asset('storage/' . $targetUser->foto));
        }

        // Tier 2: has face data — fetch from Flask, self-cache
        if ($targetUser->has_face_data && $targetUser->username) {
            $flaskUrl = config('services.flask.url', env('FLASK_SERVICE_URL', 'http://face-service:5000'));

            try {
                $listResponse = Http::timeout(5)->get("{$flaskUrl}/face-dataset/{$targetUser->username}");

                if ($listResponse->successful()) {
                    $photos = $listResponse->json('photos', []);

                    if (!empty($photos)) {
                        $photoPath = $photos[0]['photo_url'];
                        $photoResponse = Http::timeout(5)->get("{$flaskUrl}{$photoPath}");

                        if ($photoResponse->successful()) {
                            $filename = 'profile/' . $targetUser->username . '_' . time() . '.jpg';
                            Storage::disk('public')->put($filename, $photoResponse->body());

                            $targetUser->update(['foto' => $filename]);

                            return response($photoResponse->body())
                                ->header('Content-Type', 'image/jpeg')
                                ->header('Cache-Control', 'private, no-store');
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fall through to default
            }
        }

        // Tier 3: default avatar
        return response()->file(public_path('img/default-avatar.svg'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
