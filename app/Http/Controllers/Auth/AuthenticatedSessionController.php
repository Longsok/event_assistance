<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the user login view.
     * If already logged in as admin → send to admin dashboard.
     * If already logged in as user → send to user dashboard.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->hasRole('admin')
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * After login, if the user is an admin, redirect to admin dashboard.
     * Otherwise redirect to the normal user dashboard.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Prevent admin from logging in via user login form
        if (Auth::user()->hasRole('admin')) {

            Auth::logout();

            return back()->withErrors([
                'email' => 'Please use the admin login page.',
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
