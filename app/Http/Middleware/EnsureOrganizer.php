<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Admin visiting organizer routes → silently redirect to admin dashboard
        // (do NOT logout — that would destroy their admin session)
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Must have organizer role
        if (!Auth::user()->hasRole('organizer')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account does not have organizer access.']);
        }

        return $next($request);
    }
}
