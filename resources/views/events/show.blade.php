<x-app-layout>
<div class="fixed inset-0 pointer-events-none" style="z-index:0;background:radial-gradient(ellipse at 60% 0%,rgba(79,70,229,.08),transparent 60%)"></div>
<div class="relative z-10 py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <a href="{{ route('events.index') }}"
               class="inline-flex items-center gap-1 text-sm hover:underline mb-2"
               style="color:#818cf8">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                My Events
            </a>
            <h1 class="text-3xl font-bold text-white">{{ $event->title }}</h1>
            <p class="text-sm mt-1" style="color:#6b7280">
                {{ \Carbon\Carbon::parse($event->start_date)->format('M d') }}
                @if($event->start_date != $event->end_date)
                – {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                @else, {{ \Carbon\Carbon::parse($event->start_date)->format('Y') }} @endif
                @if($event->venue) · {{ $event->venue }} @endif
            </p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0 mt-1">
            <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $event->status==='ongoing'   ? 'bg-emerald-900/40 text-emerald-400' :
                   ($event->status==='completed'? 'bg-blue-900/40 text-blue-400' :
                   ($event->status==='published'? 'bg-indigo-900/40 text-indigo-300' :
                    'text-gray-400')) }}"
                style="{{ $event->status==='draft' ? 'background:rgba(255,255,255,.08)' : '' }}">
                {{ ucfirst($event->status) }}
            </span>
            <a href="{{ route('events.edit', $event) }}"
               class="px-4 py-2 text-sm font-medium rounded-xl transition"
               style="background:rgba(255,255,255,.07);color:#9ca3af;border:1px solid rgba(255,255,255,.1)"
               onmouseover="this.style.background='rgba(255,255,255,.12)'"
               onmouseout="this.style.background='rgba(255,255,255,.07)'">
                Edit
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    @php
        $tabs = [
            'overview'   => ['label'=>'Overview',   'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            'tasks'      => ['label'=>'Tasks',      'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            'guests'     => ['label'=>'Guests',     'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            'schedule'   => ['label'=>'Schedule',   'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            'budget'     => ['label'=>'Budget',     'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'attendance' => ['label'=>'Attendance', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            'invite'     => ['label'=>'Invite',     'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ];
        $currentTab = request('tab', 'overview');
    @endphp

    <div class="flex gap-1 mb-6 overflow-x-auto pb-1" style="border-bottom:1px solid rgba(255,255,255,.07)">
        @foreach($tabs as $key => $tab)
        <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
           class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition"
           style="{{ $currentTab===$key ? 'background:rgba(99,102,241,.15);color:#818cf8' : 'color:#6b7280' }}"
           onmouseover="if('{{ $currentTab }}'!=='{{ $key }}') this.style.background='rgba(255,255,255,.05)'"
           onmouseout="if('{{ $currentTab }}'!=='{{ $key }}') this.style.background='transparent'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $tab['icon'] }}"/>
            </svg>
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>

    {{-- OVERVIEW TAB --}}
    @if($currentTab === 'overview')
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label'=>'Guests',       'value'=>$event->eventGuests->count(),          'color'=>'text-white'],
            ['label'=>'Tasks Done',   'value'=>$event->tasks->where('status','done')->count().'/'.$event->tasks->count(), 'color'=>'text-indigo-400'],
            ['label'=>'Total Budget', 'value'=>'$'.number_format($event->budget?->total_budget ?? 0), 'color'=>'text-white'],
            ['label'=>'Sessions',     'value'=>$event->schedules->count(),             'color'=>'text-white'],
        ] as $s)
        <div class="rounded-2xl border p-4 text-center" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <p class="text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs mt-1" style="color:#6b7280">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Event Details --}}
    <div class="rounded-2xl border p-6 mb-6" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <h3 class="font-semibold text-white mb-4">Event Details</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="text-xs mb-0.5" style="color:#6b7280">Date</p>
                    <p class="font-medium text-white">{{ \Carbon\Carbon::parse($event->start_date)->format('D, M d Y') }}
                    @if($event->start_date != $event->end_date) — {{ \Carbon\Carbon::parse($event->end_date)->format('D, M d Y') }} @endif</p>
                </div>
            </div>
            @if($event->start_time)
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-xs mb-0.5" style="color:#6b7280">Time</p>
                    <p class="font-medium text-white">{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                    @if($event->end_time) — {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }} @endif</p>
                </div>
            </div>
            @endif
            @if($event->venue)
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                <div>
                    <p class="text-xs mb-0.5" style="color:#6b7280">Venue</p>
                    <p class="font-medium text-white">{{ $event->venue }}</p>
                    @if($event->address)<p class="text-xs" style="color:#6b7280">{{ $event->address }}</p>@endif
                </div>
            </div>
            @endif
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div>
                    <p class="text-xs mb-0.5" style="color:#6b7280">Capacity</p>
                    <p class="font-medium text-white">{{ $event->capacity }} guests</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <div>
                    <p class="text-xs mb-0.5" style="color:#6b7280">Category</p>
                    <p class="font-medium text-white">{{ $event->category?->name ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                </svg>
                <div>
                    <p class="text-xs mb-0.5" style="color:#6b7280">Type</p>
                    <p class="font-medium text-white">
                        {{ ucfirst($event->venue_type ?? 'indoor') }}
                        {{ $event->meal_provided ? '· Meal included' : '' }}
                        {{ $event->is_public ? '· Public' : '· Private' }}
                    </p>
                </div>
            </div>
        </div>
        @if($event->description)
        <div class="mt-4 pt-4" style="border-top:1px solid rgba(255,255,255,.07)">
            <p class="text-xs mb-1" style="color:#6b7280">Description</p>
            <p class="text-sm" style="color:#d1d5db">{{ $event->description }}</p>
        </div>
        @endif
    </div>

    {{-- Recent Tasks + Schedule Preview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl border overflow-hidden" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,.07)">
                <p class="font-semibold text-white">Recent Tasks</p>
                <a href="{{ request()->fullUrlWithQuery(['tab'=>'tasks']) }}" class="text-xs hover:underline" style="color:#818cf8">View all</a>
            </div>
            @forelse($event->tasks->take(5) as $task)
            <div class="flex items-center gap-3 px-5 py-3.5" style="border-bottom:1px solid rgba(255,255,255,.05)">
                <div class="w-4 h-4 rounded-full border-2 flex-shrink-0
                    {{ $task->status==='done' ? 'bg-emerald-500 border-emerald-500' : 'border-gray-600' }}"></div>
                <p class="text-sm flex-1 truncate {{ $task->status==='done' ? 'line-through' : 'text-white' }}"
                   style="{{ $task->status==='done' ? 'color:#6b7280' : '' }}">{{ $task->task_name }}</p>
                <span class="text-xs flex-shrink-0 {{ $task->status==='overdue' ? 'text-red-400' : '' }}"
                      style="{{ $task->status!=='overdue' ? 'color:#6b7280' : '' }}">
                    {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm" style="color:#6b7280">No tasks yet.</div>
            @endforelse
        </div>

        <div class="rounded-2xl border overflow-hidden" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,.07)">
                <p class="font-semibold text-white">Schedule Preview</p>
                <a href="{{ request()->fullUrlWithQuery(['tab'=>'schedule']) }}" class="text-xs hover:underline" style="color:#818cf8">View all</a>
            </div>
            @forelse($event->schedules->sortBy(['day_number','start_time'])->take(5) as $session)
            <div class="flex items-center gap-3 px-5 py-3.5" style="border-bottom:1px solid rgba(255,255,255,.05)">
                <div class="text-xs font-mono w-14 flex-shrink-0" style="color:#6b7280">
                    {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                </div>
                <p class="text-sm flex-1 truncate text-white">{{ $session->session_name }}</p>
                <span class="text-xs flex-shrink-0" style="color:#6b7280">Day {{ $session->day_number }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm" style="color:#6b7280">No sessions yet.</div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- TASKS TAB --}}
    @if($currentTab === 'tasks')
    @livewire('task-checklist', ['event' => $event])
    @endif

    {{-- GUESTS TAB --}}
    @if($currentTab === 'guests')
    @php
        $confirmed = $event->eventGuests->where('rsvp_status','confirmed')->count();
        $declined  = $event->eventGuests->where('rsvp_status','declined')->count();
        $attended  = $event->eventGuests->where('rsvp_status','attended')->count();
        $invited   = $event->eventGuests->where('rsvp_status','invited')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        @foreach([
            ['label'=>'Total',     'val'=>$event->eventGuests->count(), 'color'=>'text-white',         'bg'=>'rgba(255,255,255,.05)'],
            ['label'=>'Confirmed', 'val'=>$confirmed,                   'color'=>'text-emerald-400',   'bg'=>'rgba(16,185,129,.08)'],
            ['label'=>'Pending',   'val'=>$invited,                     'color'=>'text-amber-400',     'bg'=>'rgba(251,191,36,.08)'],
            ['label'=>'Declined',  'val'=>$declined,                    'color'=>'text-red-400',       'bg'=>'rgba(239,68,68,.08)'],
        ] as $s)
        <div class="rounded-xl border p-3 text-center" style="background:{{ $s['bg'] }};border-color:rgba(255,255,255,.07)">
            <p class="text-xl font-bold {{ $s['color'] }}">{{ $s['val'] }}</p>
            <p class="text-xs mt-1" style="color:#6b7280">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="rounded-2xl border p-5 mb-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <h3 class="font-semibold text-white mb-4">Add Guest</h3>
        <form method="POST" action="{{ route('events.guests.store', $event) }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="text" name="name" placeholder="Full Name *" required
                   class="flex-1 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                   style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            <input type="email" name="email" placeholder="Email (optional)"
                   class="flex-1 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                   style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            <input type="text" name="phone" placeholder="Phone (optional)"
                   class="flex-1 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                   style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            <button type="submit"
                    class="px-5 py-2.5 text-white text-sm font-medium rounded-xl flex-shrink-0"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                + Add
            </button>
        </form>
    </div>

    <div class="rounded-2xl border overflow-hidden" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,.07)">
            <p class="font-semibold text-white">Guest List</p>
            <a href="{{ route('events.invite.guests', $event) }}" class="text-xs hover:underline" style="color:#818cf8">View QR Cards →</a>
        </div>
        @forelse($event->eventGuests as $eg)
        <div class="flex items-center gap-4 px-5 py-3.5" style="border-bottom:1px solid rgba(255,255,255,.05)"
             onmouseover="this.style.background='rgba(255,255,255,.02)'"
             onmouseout="this.style.background='transparent'">
            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                {{ strtoupper(substr($eg->guest->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ $eg->guest->name }}</p>
                <p class="text-xs truncate" style="color:#6b7280">{{ $eg->guest->email ?? $eg->guest->phone ?? 'No contact' }}</p>
            </div>
            <span class="text-xs font-mono hidden sm:block flex-shrink-0" style="color:#6b7280">{{ $eg->guest_code }}</span>
            <form method="POST" action="{{ route('events.guests.update', [$event, $eg]) }}" class="flex-shrink-0">
                @csrf @method('PATCH')
                <select name="rsvp_status" onchange="this.form.submit()"
                        class="text-xs rounded-lg px-2 py-1.5 focus:outline-none text-white"
                        style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    @foreach(['invited','confirmed','declined','attended','cancelled'] as $status)
                    <option value="{{ $status }}" {{ $eg->rsvp_status===$status?'selected':'' }} style="background:#0d1117">
                        {{ ucfirst($status) }}
                    </option>
                    @endforeach
                </select>
            </form>
            <form method="POST" action="{{ route('events.guests.destroy', [$event, $eg]) }}"
                  onsubmit="return confirm('Remove {{ addslashes($eg->guest->name) }}?')" class="flex-shrink-0">
                @csrf @method('DELETE')
                <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center transition"
                        style="color:#6b7280"
                        onmouseover="this.style.color='#f87171';this.style.background='rgba(239,68,68,.1)'"
                        onmouseout="this.style.color='#6b7280';this.style.background='transparent'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
        @empty
        <div class="py-12 text-center text-sm" style="color:#6b7280">No guests yet. Add guests above.</div>
        @endforelse
    </div>
    @endif

    {{-- SCHEDULE TAB --}}
    @if($currentTab === 'schedule')
    @php $schedulesByDay = $event->schedules->sortBy('start_time')->groupBy('day_number'); @endphp
    @if($schedulesByDay->isEmpty())
    <div class="rounded-2xl border py-12 text-center" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <p class="text-sm" style="color:#6b7280">No sessions yet.
            <a href="{{ route('events.schedule.index', $event) }}" class="text-indigo-400 hover:underline">Add sessions</a>
        </p>
    </div>
    @else
    <div class="space-y-5">
        @foreach($schedulesByDay as $dayNum => $sessions)
        <div class="rounded-2xl border overflow-hidden" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <div class="px-5 py-3.5" style="background:rgba(99,102,241,.08);border-bottom:1px solid rgba(255,255,255,.07)">
                <p class="font-semibold text-indigo-300">Day {{ $dayNum }}
                    <span class="font-normal ml-2 text-sm" style="color:#6b7280">
                        {{ \Carbon\Carbon::parse($event->start_date)->addDays($dayNum-1)->format('D, M d Y') }}
                    </span>
                </p>
            </div>
            @foreach($sessions->sortBy('start_time') as $session)
            <div class="flex items-center gap-4 px-5 py-3.5" style="border-bottom:1px solid rgba(255,255,255,.05)">
                <div class="text-xs font-mono w-28 flex-shrink-0" style="color:#6b7280">
                    {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                    @if($session->end_time) — {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }} @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ $session->session_name }}</p>
                    @if($session->location || $session->speaker)
                    <p class="text-xs mt-0.5" style="color:#6b7280">
                        @if($session->location) 📍 {{ $session->location }} @endif
                        @if($session->speaker) · 🎤 {{ $session->speaker }} @endif
                    </p>
                    @endif
                </div>
                @if($session->duration_minutes)
                <span class="text-xs flex-shrink-0" style="color:#6b7280">{{ $session->duration_minutes }}min</span>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
    <div class="mt-4 text-center">
        <a href="{{ route('events.schedule.index', $event) }}" class="text-sm hover:underline" style="color:#818cf8">Manage full schedule →</a>
    </div>
    @endif
    @endif

    {{-- BUDGET TAB --}}
    @if($currentTab === 'budget')
    @livewire('budget-tracker', ['event' => $event])
    <div class="mt-4 text-center">
        <a href="{{ route('events.budget.index', $event) }}" class="text-sm hover:underline" style="color:#818cf8">Manage full budget →</a>
    </div>
    @endif

    {{-- ATTENDANCE TAB --}}
    @if($currentTab === 'attendance')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach([
            ['route'=>'events.attendance.index', 'label'=>'Attendance Dashboard', 'sub'=>'View check-in logs and manage attendance', 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color'=>'#818cf8','bg'=>'rgba(79,70,229,.1)'],
            ['route'=>'events.attendance.scan',  'label'=>'QR Scanner',           'sub'=>'Scan guest QR codes at the door',          'icon'=>'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'color'=>'#34d399','bg'=>'rgba(16,185,129,.08)'],
        ] as $card)
        <a href="{{ route($card['route'], $event) }}"
           class="rounded-2xl border p-6 transition"
           style="background:#0d1117;border-color:rgba(255,255,255,.07)"
           onmouseover="this.style.borderColor='rgba(99,102,241,.3)'"
           onmouseout="this.style.borderColor='rgba(255,255,255,.07)'">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:{{ $card['bg'] }}">
                <svg class="w-5 h-5" style="color:{{ $card['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <p class="font-semibold text-white">{{ $card['label'] }}</p>
            <p class="text-sm mt-1" style="color:#6b7280">{{ $card['sub'] }}</p>
        </a>
        @endforeach
    </div>
    @endif

    {{-- INVITE TAB --}}
    @if($currentTab === 'invite')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach([
            ['route'=>'events.invite.show',   'label'=>'Invite Card Settings', 'sub'=>'Customize the public invite page and QR', 'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color'=>'#818cf8','bg'=>'rgba(79,70,229,.1)'],
            ['route'=>'events.invite.guests', 'label'=>'Guest Invite Cards',   'sub'=>'Individual QR cards for each guest',       'icon'=>'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'color'=>'#a78bfa','bg'=>'rgba(167,139,250,.1)'],
        ] as $card)
        <a href="{{ route($card['route'], $event) }}"
           class="rounded-2xl border p-6 transition"
           style="background:#0d1117;border-color:rgba(255,255,255,.07)"
           onmouseover="this.style.borderColor='rgba(99,102,241,.3)'"
           onmouseout="this.style.borderColor='rgba(255,255,255,.07)'">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background:{{ $card['bg'] }}">
                <svg class="w-5 h-5" style="color:{{ $card['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <p class="font-semibold text-white">{{ $card['label'] }}</p>
            <p class="text-sm mt-1" style="color:#6b7280">{{ $card['sub'] }}</p>
        </a>
        @endforeach
    </div>
    @endif

</div>
</x-app-layout>
