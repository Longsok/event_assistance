<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>*{font-family:'Outfit',sans-serif}</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-8 text-white text-center" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                <h1 class="text-2xl font-bold">{{ $event->title }}</h1>
                <p class="text-indigo-200 mt-1">{{ $event->start_date->format('M d, Y') }}</p>
                @if($event->venue)<p class="text-indigo-200 text-sm mt-0.5">{{ $event->venue }}</p>@endif
            </div>
            <div class="p-6">
                @if(isset($isFull) && $isFull)
                <div class="text-center py-4">
                    <p class="text-red-500 font-medium">Registration is full.</p>
                </div>
                @elseif(isset($registered) && $registered)
                <div class="text-center py-4 space-y-3">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800">You are registered!</h2>
                    <p class="text-slate-500 text-sm">Welcome, {{ $guestName }}</p>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-1">Your Guest Code</p>
                        <p class="text-2xl font-mono font-bold text-indigo-700">{{ $guestCode }}</p>
                        <p class="text-xs text-slate-400 mt-1">Keep this code for check-in</p>
                    </div>
                </div>
                @elseif(isset($alreadyJoined) && $alreadyJoined)
                <div class="text-center py-4">
                    <p class="text-amber-600 font-medium">You are already registered!</p>
                </div>
                @else
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Guest Registration</h2>
                <form method="POST" action="{{ route('public.register', $event->invite_token ?? $event->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium transition">Register</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
