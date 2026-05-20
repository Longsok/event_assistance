<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
                <h2 class="text-2xl font-bold text-gray-900 mt-1">Attendance</h2>
            </div>
            <span class="text-sm text-gray-500">{{ now()->format('M d, Y') }}</span>
        </div>

        {{-- Live Stats Counter (auto-updates every 3s) --}}
        <livewire:attendance-counter :event="$event" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- QR Code Panel --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                @if($event->attendance_token)
                    <div class="text-center space-y-4">
                        <div class="inline-flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm font-medium px-4 py-2 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Check-in is LIVE
                        </div>

                        @if($qrCode)
                        <div class="inline-block p-4 bg-white border-2 border-gray-100 rounded-2xl shadow-sm">
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                                 alt="Check-in QR Code"
                                 class="w-56 h-56">
                        </div>
                        @endif

                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-1">Check-in URL</p>
                            <p class="text-xs font-mono text-gray-600 break-all">
                                {{ route('public.checkin', $event->attendance_token) }}
                            </p>
                        </div>

                        <p class="text-sm text-gray-500">Guests scan this QR with their phone to check in.</p>

                        <form method="POST" action="{{ route('events.attendance.stop', $event) }}">
                            @csrf
                            <button class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-medium rounded-xl transition">
                                ⏹ Stop Check-in
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Check-in Not Started</h3>
                            <p class="text-sm text-gray-500 mt-1">Start check-in to generate the QR code.</p>
                        </div>
                        <form method="POST" action="{{ route('events.attendance.start', $event) }}">
                            @csrf
                            <button class="w-full py-3 bg-green-600 hover:bg-green-500 text-white font-medium rounded-xl transition">
                                ▶ Start Check-in
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Manual Check-in --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Manual Check-in</h3>
                <form method="POST" action="{{ route('events.attendance.manual', $event) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Select Guest</label>
                        <select name="event_guest_id"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                            <option value="">Choose guest...</option>
                            @foreach($event->eventGuests()->with('guest')->get() as $eg)
                            <option value="{{ $eg->id }}">
                                {{ $eg->guest->name }} — {{ $eg->guest_code }}
                                @if($eg->isCheckedIn()) ✅ @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                        Check In Manually
                    </button>
                </form>

                @if(session('success'))
                <div class="mt-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mt-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Check-in Log --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Check-in Log</h3>
                <span class="text-sm text-gray-400">{{ $checkedIn->count() }} arrived</span>
            </div>
            @forelse($checkedIn as $log)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $log->scan_method === 'self' ? '📱 Self check-in' : '👤 Manual' }}</p>
                </div>
                <p class="text-sm text-gray-500 font-medium">{{ $log->checked_in_at->format('H:i') }}</p>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-4xl mb-3">👥</p>
                <p>No check-ins yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
