<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show registration form.
     * If already logged in as admin → admin dashboard.
     * If already logged in as user  → user dashboard.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->hasRole('admin')
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle registration form submission.
     * Always assigns the 'user' (organizer) role — never 'admin'.
     * Admin accounts must be created via: php artisan db:seed
     */
    public function store(Request $request): RedirectResponse
    {
        // Block if somehow already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Public registration always creates organizer (user) role — never admin
        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
