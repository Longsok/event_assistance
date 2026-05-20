<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-indigo-600 px-6 py-8 text-white text-center">
                <h1 class="text-2xl font-bold">{{ $event->title }}</h1>
                <p class="text-indigo-200 mt-1">{{ $event->start_date->format('M d, Y') }}</p>
                @if($event->venue)<p class="text-indigo-200 text-sm">📍 {{ $event->venue }}</p>@endif
            </div>

            <div class="p-6">
                @if(isset($isFull) && $isFull)
                    <div class="text-center py-4">
                        <p class="text-red-500 font-medium">Registration is full.</p>
                    </div>
                @elseif(isset($registered) && $registered)
                    <div class="text-center py-4 space-y-3">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-3xl">✅</span>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-800">You're registered!</h2>
                        <p class="text-gray-500 text-sm">Welcome, {{ $guestName }}</p>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 mb-1">Your Guest Code</p>
                            <p class="text-2xl font-mono font-bold text-indigo-700">{{ $guestCode }}</p>
                            <p class="text-xs text-gray-400 mt-1">Keep this code — you'll need it to check in</p>
                        </div>
                    </div>
                @elseif(isset($alreadyJoined) && $alreadyJoined)
                    <div class="text-center py-4 space-y-3">
                        <p class="text-yellow-600 font-medium">You're already registered!</p>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 mb-1">Your Guest Code</p>
                            <p class="text-2xl font-mono font-bold text-indigo-700">{{ $guestCode }}</p>
                        </div>
                    </div>
                @else
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Register for this event</h2>
                    <form method="POST" action="{{ route('public.register.store', $event->invite_token) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        </div>
                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium transition">
                            Register Now
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
