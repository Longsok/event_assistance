<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        $socialUser = Socialite::driver('google')
            ->setHttpClient(new Client(['verify' => false]))
            ->stateless()
            ->user();

        $user = User::updateOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'              => $socialUser->getName(),
                'google_id'         => $socialUser->getId(),
                'password'          => bcrypt(Str::random(24)),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasAnyRole(['admin', 'organizer'])) {
            $user->assignRole('organizer');
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback()
    {
        $socialUser = Socialite::driver('facebook')
            ->setHttpClient(new Client(['verify' => false]))
            ->stateless()
            ->user();

        $user = User::updateOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'              => $socialUser->getName(),
                'facebook_id'       => $socialUser->getId(),
                'password'          => bcrypt(Str::random(24)),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasAnyRole(['admin', 'organizer'])) {
            $user->assignRole('organizer');
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
