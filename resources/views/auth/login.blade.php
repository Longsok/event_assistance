<x-guest-layout>
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-white">Welcome back</h1>
        <p class="text-gray-400 text-sm mt-1">Sign in to manage your events</p>
    </div>
    @if(session('status'))
    <div class="mb-5 flex items-center gap-2 bg-green-900/40 border border-green-700/50 text-green-300 text-sm px-4 py-3 rounded-lg">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('status') }}
    </div>
    @endif
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-4 py-2.5 rounded-lg border text-white text-sm placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition {{ $errors->has('email') ? 'border-red-500 bg-gray-800' : 'border-gray-700 bg-gray-800' }}"
                   placeholder="you@example.com">
            @error('email')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="block text-sm font-medium text-gray-300">Password</label>
                @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition">Forgot password?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                   placeholder="••••••••">
            @error('password')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-indigo-600 focus:ring-indigo-500">
            <label for="remember_me" class="ml-2 text-sm text-gray-400 select-none">Keep me signed in</label>
        </div>
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-lg shadow-indigo-900/40">
            Sign in
        </button>
    </form>

    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px bg-gray-800"></div>
        <span class="text-xs text-gray-600">or continue with</span>
        <div class="flex-1 h-px bg-gray-800"></div>
    </div>

    <div class="space-y-3">
        <a href="{{ route('auth.google') }}"
           class="flex items-center justify-center gap-3 w-full py-2.5 rounded-lg border border-gray-700 bg-gray-800 hover:bg-gray-750 text-white text-sm font-medium transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
        </a>
        <a href="{{ route('auth.facebook') }}"
           class="flex items-center justify-center gap-3 w-full py-2.5 rounded-lg border border-gray-700 bg-gray-800 hover:bg-gray-750 text-white text-sm font-medium transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="#1877F2">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Continue with Facebook
        </a>
    </div>

    <p class="text-center text-sm text-gray-500 mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition">Create one free</a>
    </p>
</x-guest-layout>
