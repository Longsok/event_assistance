<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — {{ config('app.name') }}</title>
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>*{font-family:'Outfit',sans-serif}</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6" style="background:#030712">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">Admin Login</h1>
            <p class="text-gray-500 text-sm mt-1">{{ config('app.name') }} Administration</p>
        </div>
        <div class="rounded-2xl border border-gray-800 px-7 py-8" style="background:#111827">
            @if(session('status'))
            <div class="mb-5 bg-green-900/40 border border-green-700/50 text-green-300 text-sm px-4 py-3 rounded-lg">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-indigo-500">
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-semibold transition">Sign in</button>
            </form>
        </div>
    </div>
</body>
</html>
