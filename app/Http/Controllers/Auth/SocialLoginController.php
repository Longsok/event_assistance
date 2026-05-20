<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialLoginController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google callback
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            [
                'provider'    => 'google',
                'provider_id' => $googleUser->getId(),
            ],
            [
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        // Assign default role if new user
        if (!$user->hasAnyRole(['admin', 'user'])) {
            $user->assignRole('user');
        }

        Auth::login($user);

        return $this->redirectBasedOnRole($user);
    }

    // Redirect to Facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Handle Facebook callback
    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->user();

        $user = User::updateOrCreate(
            [
                'provider'    => 'facebook',
                'provider_id' => $facebookUser->getId(),
            ],
            [
                'name'              => $facebookUser->getName(),
                'email'             => $facebookUser->getEmail(),
                'avatar'            => $facebookUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasAnyRole(['admin', 'user'])) {
            $user->assignRole('user');
        }

        Auth::login($user);

        return $this->redirectBasedOnRole($user);
    }

    // Redirect after login based on role
    private function redirectBasedOnRole(User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
}
