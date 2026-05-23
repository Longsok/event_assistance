<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ $event->title }}
            </a>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">Attendance</h2>
        </div>
        <span class="text-sm text-slate-500">{{ now()->format('M d, Y') }}</span>
    </div>
    <livewire:attendance-counter :event="$event" />
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            @if($event->attendance_token)
            <div class="text-center space-y-4">
                <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium px-4 py-2 rounded-full">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Check-in is LIVE
                </div>
                @if($qrCode)
                <div class="inline-block p-4 bg-white border-2 border-slate-100 rounded-2xl shadow-sm">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" class="w-56 h-56">
                </div>
                @endif
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-400 mb-1">Check-in URL</p>
                    <p class="text-xs font-mono text-slate-600 break-all">{{ route('public.checkin', $event->attendance_token) }}</p>
                </div>
                <form method="POST" action="{{ route('events.attendance.stop', $event) }}">
                    @csrf
                    <button class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-medium rounded-xl transition">Stop Check-in</button>
                </form>
            </div>
            @else
            <div class="text-center space-y-4">
                <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800">Check-in Not Started</h3>
                    <p class="text-sm text-slate-500 mt-1">Start check-in to generate the QR code.</p>
                </div>
                <form method="POST" action="{{ route('events.attendance.start', $event) }}">
                    @csrf
                    <button class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl transition">Start Check-in</button>
                </form>
            </div>
            @endif
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Manual Check-in</h3>
            <form method="POST" action="{{ route('events.attendance.manual', $event) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Select Guest</label>
                    <select name="event_guest_id" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        <option value="">Choose guest...</option>
                        @foreach($event->eventGuests()->with('guest')->get() as $eg)
                        <option value="{{ $eg->id }}">{{ $eg->guest->name }} — {{ $eg->guest_code }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Check In Manually</button>
            </form>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Check-in Log</h3>
            <span class="text-sm text-slate-400">{{ $checkedIn->count() }} arrived</span>
        </div>
        @forelse($checkedIn as $log)
        <div class="flex items-center gap-4 px-6 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="font-medium text-slate-800">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                <p class="text-xs text-slate-400">{{ $log->scan_method==='self' ? 'Self check-in' : 'Manual check-in' }}</p>
            </div>
            <p class="text-sm text-slate-500 font-medium">{{ $log->checked_in_at->format('H:i') }}</p>
        </div>
        @empty
        <div class="px-6 py-12 text-center">
            <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <p class="text-slate-400">No check-ins yet.</p>
        </div>
        @endforelse
    </div>
</div>
</x-app-layout>
