<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check In — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-green-600 px-6 py-8 text-white text-center">
                <h1 class="text-2xl font-bold">Check In</h1>
                <p class="text-green-200 mt-1">{{ $event->title }}</p>
            </div>
            <div class="p-6">
                @if(isset($alreadyCheckedIn) && $alreadyCheckedIn)
                    <div class="text-center py-4 space-y-3">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-3xl">⚠️</span>
                        </div>
                        <p class="font-semibold text-gray-800">Already Checked In</p>
                        <p class="text-gray-500 text-sm">{{ $guestName }} checked in at {{ $checkedInAt->format('H:i') }}</p>
                    </div>
                @else
                    <p class="text-gray-600 text-sm mb-5">Enter your guest code and name to check in.</p>
                    <form method="POST" action="{{ route('public.checkin.store', $event->attendance_token) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Guest Code *</label>
                            <input type="text" name="guest_code" value="{{ old('guest_code') }}" placeholder="e.g. WEDD-2026-001"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-green-400 uppercase" required>
                            @error('guest_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-400" required>
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-500 text-white rounded-lg font-medium transition">
                            Check In
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
