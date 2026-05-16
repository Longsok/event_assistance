<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login — {{ config('app.name', 'Event Assistance') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-6 py-8">

        {{-- Logo / Brand --}}
        <div class="flex flex-col items-center mb-8">
            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-indigo-900/50">
                {{-- Shield icon --}}
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Admin Panel</h1>
            <p class="text-gray-400 text-sm mt-1">{{ config('app.name', 'Event Assistance System') }}</p>
        </div>

        {{-- Card --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl px-8 py-8">

            {{-- Session Status (e.g. password reset success) --}}
            @if (session('status'))
                <div class="mb-4 text-sm text-green-400 bg-green-900/30 border border-green-800 rounded-lg px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">
                        Email address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border
                               {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-700 focus:ring-indigo-500' }}
                               text-white placeholder-gray-500 text-sm
                               focus:outline-none focus:ring-2 focus:border-transparent
                               transition"
                        placeholder="admin@example.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border border-gray-700
                               text-white placeholder-gray-500 text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                               transition"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center mb-6">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-indigo-600 focus:ring-indigo-500"
                    >
                    <label for="remember" class="ml-2 text-sm text-gray-400">Keep me signed in</label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700
                           text-white font-semibold py-2.5 px-4 rounded-lg text-sm
                           transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900
                           shadow-lg shadow-indigo-900/40"
                >
                    Sign in to Admin Panel
                </button>
            </form>
        </div>

        {{-- Back to user site --}}
        <p class="text-center text-sm text-gray-600 mt-6">
            Not an admin?
            <a href="{{ route('admin.login') }}" class="text-indigo-400 hover:text-indigo-300 transition">
                Go to user login
            </a>
        </p>
    </div>

</body>
</html>
