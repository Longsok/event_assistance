<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check In — {{ $event->title ?? 'Event' }}</title>
    @include('partials.assets')
    <style>*{font-family:'Outfit',sans-serif}</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-8 text-white text-center" style="background:linear-gradient(135deg,#059669,#047857)">
                <h1 class="text-2xl font-bold">Check In</h1>
                <p class="text-emerald-200 mt-1">{{ $event->title ?? 'Event Check-in' }}</p>
            </div>
            <div class="p-6">

                @if(isset($checkinClosed) && $checkinClosed)
                {{-- ===== Check-in not available ===== --}}
                <div class="text-center py-4 space-y-3">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <p class="font-semibold text-slate-800">Check-in unavailable</p>
                    <p class="text-slate-500 text-sm">{{ $closedReason ?? 'Check-in for this event is not open right now.' }}</p>
                    <p class="text-slate-400 text-xs">Please ask the event organizer for help.</p>
                </div>

                @elseif(isset($alreadyCheckedIn) && $alreadyCheckedIn)
                {{-- ===== Already checked in ===== --}}
                <div class="text-center py-4 space-y-3">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <p class="font-semibold text-slate-800">Already Checked In</p>
                    <p class="text-slate-500 text-sm">{{ $guestName }} checked in at {{ \Carbon\Carbon::parse($checkedInAt)->format('H:i') }}</p>
                </div>

                @else
                {{-- ===== Check-in form ===== --}}
                <p class="text-slate-600 text-sm mb-5">Enter your guest code to check in.</p>
                <form method="POST" action="{{ route('public.checkin.store', $event->attendance_token) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Guest Code *</label>
                        <input type="text" name="guest_code" value="{{ old('guest_code') }}" placeholder="e.g. WEDD-2026-001" required
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm font-mono uppercase focus:outline-none focus:border-emerald-400">
                        @error('guest_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Your Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-emerald-400">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-medium transition">Check In</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>