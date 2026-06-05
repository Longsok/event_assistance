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
            ->setHttpClient(new Client(['verify' => app()->environment('local') ? false : true]))
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

    // Facebook does not always provide email — use facebook_id as fallback
    $email = $socialUser->getEmail()
        ?? $socialUser->getId() . '@facebook.placeholder.com';

    // Match by facebook_id first, not email (email can be null)
    $user = User::where('facebook_id', $socialUser->getId())->first();

    if (!$user) {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $socialUser->getName(),
                'facebook_id'       => $socialUser->getId(),
                'password'          => bcrypt(Str::random(24)),
                'email_verified_at' => now(),
            ]
        );
    }

    // Always sync facebook_id in case they registered via email before
    $user->update(['facebook_id' => $socialUser->getId()]);

    if (!$user->hasAnyRole(['admin', 'organizer'])) {
        $user->assignRole('organizer');
    }

    Auth::login($user);
    return redirect()->route('dashboard');
}
}
