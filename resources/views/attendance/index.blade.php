<x-app-layout>
<div class="py-6 px-4 sm:px-6 max-w-5xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-sm hover:underline" style="color:#818cf8">← {{ $event->title }}</a>
            <h2 class="text-2xl font-bold text-white mt-1">Attendance</h2>
        </div>
        <span class="text-sm" style="color:#6b7280">{{ now()->format('M d, Y') }}</span>
    </div>

    {{-- Live counter --}}
    @livewire('attendance-counter', ['event' => $event])

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Event QR for public check-in --}}
        <div class="rounded-2xl border p-6" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            @if($event->attendance_token)
            <div class="text-center space-y-4">
                <div class="inline-flex items-center gap-2 text-sm font-medium px-4 py-2 rounded-full"
                     style="background:rgba(16,185,129,.1);border:1px solid rgba(52,211,153,.2);color:#34d399">
                    <span class="w-2 h-2 rounded-full" style="background:#10b981;animation:pulse 2s infinite"></span>
                    Check-in is LIVE
                </div>
                @if($qrCode)
                <div class="inline-block p-4 bg-white rounded-2xl shadow-lg">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" class="w-48 h-48">
                </div>
                @endif
                <div class="rounded-xl p-3" style="background:rgba(255,255,255,.05)">
                    <p class="text-xs mb-1" style="color:#6b7280">Check-in URL</p>
                    <p class="text-xs font-mono break-all" style="color:#9ca3af">
                        {{ route('public.checkin', $event->attendance_token) }}
                    </p>
                </div>
                <form method="POST" action="{{ route('events.attendance.stop', $event) }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 text-sm font-medium rounded-xl transition"
                            style="background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.2)"
                            onmouseover="this.style.background='rgba(239,68,68,.2)'"
                            onmouseout="this.style.background='rgba(239,68,68,.1)'">
                        Stop Check-in
                    </button>
                </form>
            </div>
            @else
            <div class="text-center py-6">
                <p class="text-sm mb-4" style="color:#6b7280">Check-in is not active yet.</p>
                <form method="POST" action="{{ route('events.attendance.start', $event) }}">
                    @csrf
                    <button type="submit"
                            class="px-6 py-2.5 text-white text-sm font-medium rounded-xl"
                            style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        Start Check-in
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Manual check-in --}}
        <div class="rounded-2xl border p-6" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <h3 class="font-semibold text-white mb-4">Manual Check-in</h3>
            <form method="POST" action="{{ route('events.attendance.manual', $event) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1.5" style="color:#9ca3af">Select Guest</label>
                    <select name="event_guest_id"
                            class="w-full rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        <option value="" style="background:#0d1117">Choose guest...</option>
                        @foreach($event->eventGuests as $eg)
                        <option value="{{ $eg->id }}" style="background:#0d1117">
                            {{ $eg->guest->name }}
                            {{ $eg->rsvp_status === 'attended' ? '✓' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="w-full py-2.5 text-white text-sm font-medium rounded-xl"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    Check In Manually
                </button>
            </form>

            @if(session('success'))
            <div class="mt-4 p-3 rounded-xl text-sm" style="background:rgba(16,185,129,.1);color:#34d399;border:1px solid rgba(52,211,153,.2)">
                ✓ {{ session('success') }}
            </div>
            @endif
        </div>
    </div>

    {{-- QR Scanner link --}}
    <a href="{{ route('events.attendance.scan', $event) }}"
       class="flex items-center gap-4 rounded-2xl border p-5 transition group"
       style="background:#0d1117;border-color:rgba(255,255,255,.07)"
       onmouseover="this.style.borderColor='rgba(99,102,241,.4)'"
       onmouseout="this.style.borderColor='rgba(255,255,255,.07)'">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(79,70,229,.12)">
            <svg class="w-5 h-5" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-white">Open QR Scanner</p>
            <p class="text-sm" style="color:#6b7280">Scan guest QR codes at the door with your phone camera</p>
        </div>
        <svg class="w-5 h-5 group-hover:translate-x-1 transition" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    {{-- Check-in log --}}
    @if($attendanceLogs->count())
    <div class="rounded-2xl border overflow-hidden" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,.07)">
            <h3 class="font-semibold text-white">Check-in Log</h3>
        </div>
        @foreach($attendanceLogs as $log)
        <div class="flex items-center gap-4 px-5 py-3.5" style="border-bottom:1px solid rgba(255,255,255,.05)">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                 style="background:linear-gradient(135deg,#059669,#047857)">
                {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-white">{{ $log->eventGuest->guest->name ?? '—' }}</p>
                <p class="text-xs" style="color:#6b7280">{{ $log->scan_method ?? 'manual' }}</p>
            </div>
            <span class="text-xs" style="color:#6b7280">{{ $log->checked_in_at->format('H:i') }}</span>
        </div>
        @endforeach
    </div>
    @endif
</div>
<style>@keyframes pulse{0%,100%{opacity:.5}50%{opacity:1}}</style>
</x-app-layout>
