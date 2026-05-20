<x-guest-layout>

    {{-- Heading --}}
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-white">Welcome back</h1>
        <p class="text-gray-400 text-sm mt-1">Sign in to manage your events</p>
    </div>

    {{-- Session Status (e.g. password reset success message) --}}
    @if (session('status'))
        <div class="mb-5 flex items-center gap-2 bg-green-900/40 border border-green-700/50 text-green-300 text-sm px-4 py-3 rounded-lg">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
                class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border text-white text-sm placeholder-gray-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                       {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }}"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-indigo-400 hover:text-indigo-300 transition">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm placeholder-gray-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            >
            @error('password')
                <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-gray-900"
            >
            <label for="remember_me" class="ml-2 text-sm text-gray-400 select-none">Keep me signed in</label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700
                   text-white font-semibold py-2.5 px-4 rounded-lg text-sm
                   transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900
                   shadow-lg shadow-indigo-900/40 mt-1"
        >
            Sign in
        </button>
    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px bg-gray-800"></div>
        <span class="text-xs text-gray-600">or</span>
        <div class="flex-1 h-px bg-gray-800"></div>
    </div>

    {{-- Register link --}}
    <p class="text-center text-sm text-gray-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition">
            Create one free
        </a>
    </p>

    <div class="mt-4 space-y-2">
    <a href="{{ route('auth.google') }}"
       class="w-full flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
        <img src="https://www.google.com/favicon.ico" class="w-4 h-4">
        Continue with Google
    </a>
    <a href="{{ route('auth.facebook') }}"
       class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg text-sm text-white transition">
        Continue with Facebook
    </a>
</div>

</x-guest-layout>
