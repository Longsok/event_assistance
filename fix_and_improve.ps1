# Run from project root:
# powershell -ExecutionPolicy Bypass -File fix_and_improve.ps1

Write-Host "Starting fixes and improvements..." -ForegroundColor Cyan

# ══════════════════════════════════════════════════════════
# 1. FIX QR CODE — attendance/index.blade.php
# ══════════════════════════════════════════════════════════
Write-Host "`n[1/3] Fixing QR Code view..." -ForegroundColor Yellow

Set-Content "resources\views\attendance\index.blade.php" @'
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

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['checked_in'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Checked In</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $stats['expected'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Expected</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
                @php $rate = $stats['expected'] > 0 ? round(($stats['checked_in'] / $stats['expected']) * 100) : 0; @endphp
                <p class="text-3xl font-bold text-indigo-600">{{ $rate }}%</p>
                <p class="text-sm text-gray-500 mt-1">Attendance Rate</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $stats['expected'] - $stats['checked_in'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Not Yet Arrived</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- QR Code Panel --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                @if($event->attendance_token)
                    <div class="text-center space-y-4">
                        <div class="inline-flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm font-medium px-4 py-2 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Check-in is LIVE
                        </div>

                        {{-- QR Code — base64 SVG --}}
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

                        <p class="text-sm text-gray-500">Guests scan this QR with their phone camera to check in instantly.</p>

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
                            <p class="text-sm text-gray-500 mt-1">Start check-in to generate the QR code for guests.</p>
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
                <span class="text-sm text-gray-400">{{ $checkedIn->count() }} guests arrived</span>
            </div>
            @forelse($checkedIn as $log)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $log->scan_method === 'self' ? '📱 Self check-in' : '👤 Manual by staff' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-700">{{ $log->checked_in_at->format('H:i') }}</p>
                    <p class="text-xs text-gray-400">{{ $log->checked_in_at->format('M d') }}</p>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-4xl mb-3">👥</p>
                <p>No check-ins yet. Start check-in and share the QR code.</p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
'@

# ══════════════════════════════════════════════════════════
# 2. STYLE IMPROVEMENTS — events/index, events/show, dashboard
# ══════════════════════════════════════════════════════════
Write-Host "[2/3] Improving styles..." -ForegroundColor Yellow

# Better dashboard
Set-Content "resources\views\dashboard\organizer.blade.php" @'
<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

        {{-- Welcome --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}</h1>
                <p class="text-gray-500 text-sm mt-1">Here's what's happening with your events today.</p>
            </div>
            <a href="{{ route('events.create') }}"
               class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
                <span>+</span> New Event
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label'=>'Total Events','value'=>$stats['total_events'],'icon'=>'🗓️','color'=>'bg-indigo-50 border-indigo-100'],
                ['label'=>'Total Guests','value'=>$stats['total_guests'],'icon'=>'👥','color'=>'bg-green-50 border-green-100'],
                ['label'=>'Overdue Tasks','value'=>$stats['overdue_tasks'],'icon'=>'⚠️','color'=>'bg-red-50 border-red-100'],
                ['label'=>'Contributions','value'=>'$'.number_format($stats['total_contributions'],0),'icon'=>'💰','color'=>'bg-yellow-50 border-yellow-100'],
            ] as $stat)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">{{ $stat['icon'] }}</span>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                        <p class="text-xs text-gray-500">{{ $stat['label'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Upcoming Events --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Upcoming Events</h3>
                    <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>
                </div>
                @forelse($upcomingEvents as $event)
                <a href="{{ route('events.show', $event) }}"
                   class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex flex-col items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-indigo-700 leading-none">{{ $event->start_date->format('M') }}</span>
                        <span class="text-lg font-bold text-indigo-900 leading-none">{{ $event->start_date->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 truncate">{{ $event->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $event->category->name ?? 'No category' }} · {{ $event->event_guests_count }} guests</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0
                        {{ $event->status === 'ongoing' ? 'bg-green-100 text-green-700' :
                           ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700') }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </a>
                @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-4xl mb-3">🗓️</p>
                    <p class="text-gray-500 mb-4">No upcoming events yet.</p>
                    <a href="{{ route('events.create') }}"
                       class="inline-block px-4 py-2 bg-indigo-600 text-white text-sm rounded-xl hover:bg-indigo-500 transition">
                        Create your first event
                    </a>
                </div>
                @endforelse
            </div>

            {{-- Overdue Tasks --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Overdue Tasks</h3>
                </div>
                @forelse($overdueTasks as $task)
                <div class="px-5 py-3 border-b border-gray-100 last:border-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $task->task_name }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-xs text-gray-400 truncate">{{ $task->event->title }}</p>
                        <span class="text-xs text-red-500 font-medium flex-shrink-0 ml-2">{{ $task->due_date->format('M d') }}</span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <p class="text-3xl mb-2">✅</p>
                    <p class="text-gray-400 text-sm">No overdue tasks!</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Check-ins --}}
        @if($recentCheckIns->count())
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Recent Check-ins</h3>
            </div>
            <div class="px-6 py-4 flex flex-wrap gap-3">
                @foreach($recentCheckIns as $log)
                <div class="flex items-center gap-2 bg-green-50 border border-green-100 rounded-xl px-3 py-2">
                    <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-800">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $log->checked_in_at->format('H:i') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
'@

# ══════════════════════════════════════════════════════════
# 3. ADD SEARCH/FILTER — events/index with better UI
# ══════════════════════════════════════════════════════════
Write-Host "[3/3] Adding search and filter improvements..." -ForegroundColor Yellow

Set-Content "resources\views\events\index.blade.php" @'
<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">My Events</h2>
                <p class="text-gray-500 text-sm mt-1">{{ $events->total() }} events total</p>
            </div>
            <a href="{{ route('events.create') }}"
               class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
                + New Event
            </a>
        </div>

        {{-- Search & Filter Bar --}}
        <form method="GET" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-48">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search events..."
                               class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                </div>
                <select name="status" class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                    <option value="">All Status</option>
                    @foreach(['draft','published','ongoing','completed','archived'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                    Search
                </button>
                @if(request()->hasAny(['search','status']))
                <a href="{{ route('events.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition">
                    Clear
                </a>
                @endif
            </div>
        </form>

        {{-- Status Filter Pills --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('events.index') }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition
                      {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-indigo-300' }}">
                All
            </a>
            @foreach(['draft'=>'📝','published'=>'📢','ongoing'=>'🟢','completed'=>'✅','archived'=>'📦'] as $s => $icon)
            <a href="{{ route('events.index', array_merge(request()->except('status','page'), ['status'=>$s])) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition
                      {{ request('status') === $s ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-indigo-300' }}">
                {{ $icon }} {{ ucfirst($s) }}
            </a>
            @endforeach
        </div>

        {{-- Events Grid --}}
        @if($events->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($events as $event)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden group">
                {{-- Color bar based on status --}}
                <div class="h-1.5 w-full
                    {{ $event->status === 'ongoing' ? 'bg-green-500' :
                       ($event->status === 'completed' ? 'bg-blue-500' :
                       ($event->status === 'draft' ? 'bg-gray-300' : 'bg-indigo-500')) }}">
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $event->status === 'ongoing' ? 'bg-green-100 text-green-700' :
                               ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' :
                               ($event->status === 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700')) }}">
                            {{ ucfirst($event->status) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $event->category->name ?? '-' }}</span>
                    </div>

                    <h3 class="font-semibold text-gray-900 text-lg mb-1 group-hover:text-indigo-600 transition">
                        {{ $event->title }}
                    </h3>

                    <div class="flex items-center gap-1 text-sm text-gray-500 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $event->start_date->format('M d') }}
                        @if($event->start_date->ne($event->end_date)) – {{ $event->end_date->format('M d, Y') }}
                        @else, {{ $event->start_date->format('Y') }}
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-400 mb-4 pb-4 border-b border-gray-100">
                        <span class="flex items-center gap-1">👥 {{ $event->event_guests_count }} guests</span>
                        @if($event->venue)
                        <span class="flex items-center gap-1 truncate ml-2">📍 {{ Str::limit($event->venue, 20) }}</span>
                        @endif
                    </div>

                    <a href="{{ route('events.show', $event) }}"
                       class="block w-full text-center py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium rounded-xl transition">
                        Manage Event →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $events->links() }}</div>
        @else
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-16 text-center">
            <p class="text-5xl mb-4">🗓️</p>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                {{ request()->hasAny(['search','status']) ? 'No events match your search' : 'No events yet' }}
            </h3>
            <p class="text-gray-500 text-sm mb-6">
                {{ request()->hasAny(['search','status']) ? 'Try different search terms or clear filters.' : 'Create your first event to get started.' }}
            </p>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('events.index') }}" class="inline-block px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-xl transition">
                Clear Filters
            </a>
            @else
            <a href="{{ route('events.create') }}" class="inline-block px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                Create First Event
            </a>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>
'@

# Better guests/index with search
Set-Content "resources\views\guests\index.blade.php" @'
<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Guest Book</h2>
                <p class="text-gray-500 text-sm mt-1">{{ $guests->total() }} guests total</p>
            </div>
            <a href="{{ route('guests.create') }}"
               class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
                + Add Guest
            </a>
        </div>

        {{-- Search --}}
        <form method="GET" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="flex gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name or email..."
                           class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('guests.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition">
                    Clear
                </a>
                @endif
            </div>
        </form>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">Guest</th>
                        <th class="px-6 py-3 text-left">Phone</th>
                        <th class="px-6 py-3 text-left">Events</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($guests as $guest)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($guest->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $guest->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $guest->email ?? 'No email' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $guest->phone ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                                {{ $guest->event_guests_count }} events
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('guests.edit', $guest) }}"
                                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                                <form method="POST" action="{{ route('guests.destroy', $guest) }}"
                                      onsubmit="return confirm('Delete {{ $guest->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <p class="text-3xl mb-3">👥</p>
                            <p class="text-gray-500">
                                {{ request('search') ? 'No guests match your search.' : 'No guests yet. Add your first guest!' }}
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $guests->links() }}</div>
    </div>
</x-app-layout>
'@

Write-Host ""
Write-Host "✅ All improvements applied!" -ForegroundColor Green
Write-Host ""
Write-Host "Now run:" -ForegroundColor Yellow
Write-Host "  composer require simplesoftwareio/simple-qrcode"
Write-Host "  php artisan optimize:clear"
