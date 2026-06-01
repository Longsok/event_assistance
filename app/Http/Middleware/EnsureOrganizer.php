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
        if (! Auth::guard('web')->check()) {
            return redirect()->route('login')->with('status', 'Please log in to continue.');
        }

        if (! Auth::guard('web')->user()->hasRole('organizer')) {
            Auth::guard('web')->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account does not have organizer access.']);
        }

        return $next($request);
    }
}
