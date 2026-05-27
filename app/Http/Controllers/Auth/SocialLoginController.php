<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['provider' => 'google', 'provider_id' => $googleUser->getId()],
            [
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'avatar'            => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasAnyRole(['admin', 'organizer'])) {
            $user->assignRole('organizer');
        }

        Auth::login($user);
        return $this->redirectBasedOnRole($user);
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->user();

        $user = User::updateOrCreate(
            ['provider' => 'facebook', 'provider_id' => $facebookUser->getId()],
            [
                'name'              => $facebookUser->getName(),
                'email'             => $facebookUser->getEmail(),
                'avatar'            => $facebookUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasAnyRole(['admin', 'organizer'])) {
            $user->assignRole('organizer');
        }

        Auth::login($user);
        return $this->redirectBasedOnRole($user);
    }

    private function redirectBasedOnRole(User $user)
    {
        return $user->hasRole('admin')
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }
}
