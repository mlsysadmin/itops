<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Redirect to Google for authentication
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // 🔹 List of pre-approved admin users (used for first-time auto-creation)
        $approvedUsers = [
            'john.castillo@mlhuillier.com' => ['role' => 'admin', 'position' => 'Network Admin'],
        ];

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        // 🔹 Check if user exists in DB
        $user = User::where('email', $email)->first();

        if (!$user && array_key_exists($email, $approvedUsers)) {
            // 🔹 Create admin user if not yet in DB, but in approved list
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(32)),
                'role' => $approvedUsers[$email]['role'],
                'position' => $approvedUsers[$email]['position'],
            ]);
        }

        if (!$user) {
            // 🔒 Block access if user is not in DB and not an approved admin
            abort(403, 'Unauthorized user');
        }

        // 🔐 Block login if Google ID doesn't match
        if ($user->google_id && $user->google_id !== $googleId) {
            abort(403, 'Unauthorized Google account');
        }

        // 🔄 Update missing fields
        $user->update([
            'name' => $user->name ?? $googleUser->getName(),
            'google_id' => $googleId,
            'avatar' => $user->avatar ?? $googleUser->getAvatar(),
            'password' => $user->password ?? bcrypt(Str::random(32)),
        ]);

        Auth::login($user);

        // ✅ Redirect based on role
        if ($user->role === 'admin') {
            return redirect('admin/dashboard');
        }

        return redirect('/dashboard');
    }



}
