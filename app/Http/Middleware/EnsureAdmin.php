<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     * Allows only authenticated users who have the 'admin' role.
     * All others are redirected to the admin login page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login')
                ->with('status', 'Please log in to access the admin panel.');
        }

        if (! Auth::user()->hasRole('admin')) {
            // They are logged in but not admin — log them out of this session
            // so they can't accidentally linger and then navigate to admin routes
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'You do not have permission to access the admin panel.']);
        }

        return $next($request);
    }

    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('admin*')) {
            return route('admin.login');
        }
        return $request->expectsJson() ? null : route('login');
    }
}
