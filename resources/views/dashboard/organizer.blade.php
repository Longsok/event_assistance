<x-app-layout>

{{-- Background effects --}}
<div class="fixed inset-0 pointer-events-none overflow-hidden" style="z-index:0">
    <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:800px;height:400px;background:radial-gradient(ellipse at 50% 0%,rgba(79,70,229,.15),transparent 70%)"></div>
    <div style="position:absolute;inset:0;background-image:linear-gradient(var(--grid,rgba(255,255,255,.02)) 1px,transparent 1px),linear-gradient(90deg,var(--grid,rgba(255,255,255,.02)) 1px,transparent 1px);background-size:48px 48px"></div>
</div>

<div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-7">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-medium uppercase tracking-widest mb-1" style="color:#6366f1">
                {{ now()->format('l, M d Y') }}
            </p>
            <h1 class="text-2xl font-bold" style="color:var(--text-strong)">
                {{ now()->hour < 12 ? 'Good morning' : (now()->hour < 18 ? 'Good afternoon' : 'Good evening') }},
                {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h1>
            <p class="text-sm mt-1" style="color:var(--text-soft)">Here's what's happening with your events.</p>
        </div>
        <a href="{{ route('events.create') }}"
           class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold rounded-xl transition"
           style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 14px rgba(79,70,229,.4)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label'=>'Total Events',   'value'=>$stats['total_events'],   'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color'=>'#818cf8','bg'=>'rgba(79,70,229,.12)','border'=>'rgba(99,102,241,.25)'],
            ['label'=>'Total Guests',   'value'=>$stats['total_guests'],   'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color'=>'#34d399','bg'=>'rgba(16,185,129,.1)','border'=>'rgba(52,211,153,.2)'],
            ['label'=>'Overdue Tasks',  'value'=>$stats['overdue_tasks'],  'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color'=>'#fb923c','bg'=>'rgba(251,146,60,.1)','border'=>'rgba(251,146,60,.2)'],
            ['label'=>'Contributions',  'value'=>'$'.number_format($stats['total_contributions'],0), 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color'=>'#f472b6','bg'=>'rgba(244,114,182,.1)','border'=>'rgba(244,114,182,.2)'],
        ];
        @endphp
        @foreach($statCards as $s)
        <div class="rounded-2xl p-5 border" style="background:var(--panel);border-color:{{ $s['border'] }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background:{{ $s['bg'] }}">
                <svg class="w-5 h-5" style="color:{{ $s['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $s['icon'] }}"/>
                </svg>
            </div>
            <p class="text-2xl font-bold" style="color:var(--text-strong)">{{ $s['value'] }}</p>
            <p class="text-xs mt-1" style="color:var(--text-soft)">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Upcoming Events + Overdue Tasks --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Upcoming Events --}}
        <div class="lg:col-span-2 rounded-2xl overflow-hidden border" style="background:var(--panel);border-color:var(--border)">
            <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--border)">
                <h3 class="font-semibold" style="color:var(--text-strong)">Upcoming Events</h3>
                <a href="{{ route('events.index') }}" class="text-xs hover:underline" style="color:#818cf8">View all →</a>
            </div>
            @forelse($upcomingEvents as $event)
            <a href="{{ route('events.show', $event) }}"
               class="flex items-center gap-4 px-6 py-4 transition group"
               style="border-bottom:1px solid var(--border-soft)"
               onmouseover="this.style.background='var(--hover)'"
               onmouseout="this.style.background='transparent'">
                <div class="w-12 h-12 rounded-xl flex flex-col items-center justify-center flex-shrink-0 border"
                     style="background:rgba(79,70,229,.12);border-color:rgba(99,102,241,.25)">
                    <span class="text-xs font-bold leading-none" style="color:#818cf8">{{ $event->start_date->format('M') }}</span>
                    <span class="text-lg font-bold leading-none" style="color:var(--text-strong)">{{ $event->start_date->format('d') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate group-hover:text-indigo-300 transition" style="color:var(--text-strong)">{{ $event->title }}</p>
                    <p class="text-xs mt-0.5 truncate" style="color:var(--text-soft)">
                        {{ $event->category->name ?? '—' }}
                        @if($event->venue) · {{ $event->venue }} @endif
                        · {{ $event->event_guests_count ?? 0 }} guests
                    </p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0
                    {{ $event->status==='ongoing'   ? 'bg-emerald-900/40 text-emerald-400' :
                       ($event->status==='draft'    ? 'bg-gray-800 text-gray-400' :
                       ($event->status==='completed'? 'bg-blue-900/40 text-blue-400' :
                                                      'bg-indigo-900/40 text-indigo-300')) }}">
                    {{ ucfirst($event->status) }}
                </span>
            </a>
            @empty
            <div class="px-6 py-14 text-center">
                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:rgba(79,70,229,.1)">
                    <svg class="w-7 h-7" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm mb-4" style="color:var(--text-soft)">No upcoming events yet.</p>
                <a href="{{ route('events.create') }}"
                   class="inline-block px-5 py-2 text-white text-sm font-medium rounded-xl transition"
                   style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    Create your first event
                </a>
            </div>
            @endforelse
        </div>

        {{-- Overdue Tasks --}}
        <div class="rounded-2xl overflow-hidden border" style="background:var(--panel);border-color:var(--border)">
            <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border)">
                <h3 class="font-semibold" style="color:var(--text-strong)">Overdue Tasks</h3>
                @if($stats['overdue_tasks'] > 0)
                <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:rgba(251,146,60,.15);color:#fb923c">
                    {{ $stats['overdue_tasks'] }}
                </span>
                @endif
            </div>
            @forelse($overdueTasks as $task)
            <div class="px-5 py-3.5" style="border-bottom:1px solid var(--border-soft)">
                <div class="flex items-start gap-2">
                    <div class="w-1.5 h-1.5 rounded-full mt-2 flex-shrink-0" style="background:#fb923c"></div>
                    <div class="min-w-0">
                        <p class="text-sm truncate" style="color:var(--text-strong)">{{ $task->task_name }}</p>
                        <div class="flex items-center justify-between gap-2 mt-0.5">
                            <p class="text-xs truncate" style="color:var(--text-soft)">{{ $task->event->title }}</p>
                            <span class="text-xs font-medium flex-shrink-0" style="color:#fb923c">
                                {{ $task->due_date->format('M d') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <div class="w-10 h-10 rounded-xl mx-auto mb-3 flex items-center justify-center" style="background:rgba(52,211,153,.1)">
                    <svg class="w-5 h-5" style="color:#34d399" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm" style="color:var(--text-soft)">All tasks on track!</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Check-ins --}}
    @if($recentCheckIns->count())
    <div class="rounded-2xl overflow-hidden border" style="background:var(--panel);border-color:var(--border)">
        <div class="px-6 py-4" style="border-bottom:1px solid var(--border)">
            <h3 class="font-semibold flex items-center gap-2" style="color:var(--text-strong)">
                <span class="w-2 h-2 rounded-full bg-emerald-400" style="animation:pulse 2s infinite"></span>
                Recent Check-ins
            </h3>
        </div>
        <div class="px-6 py-4 flex flex-wrap gap-3">
            @foreach($recentCheckIns as $log)
            <div class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 border"
                 style="background:rgba(16,185,129,.08);border-color:rgba(52,211,153,.2)">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                     style="background:linear-gradient(135deg,#059669,#047857)">
                    {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium" style="color:var(--text-strong)">{{ $log->eventGuest->guest->name ?? '—' }}</p>
                    <p class="text-xs" style="color:var(--text-soft)">{{ $log->checked_in_at->format('H:i') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach([
            ['route'=>'events.create',  'label'=>'New Event',     'sub'=>'Plan something new',      'icon'=>'M12 4v16m8-8H4',                        'color'=>'#818cf8','bg'=>'rgba(79,70,229,.1)','border'=>'rgba(99,102,241,.2)'],
            ['route'=>'guests.index',   'label'=>'Guest Book',    'sub'=>'Manage your guests',      'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color'=>'#34d399','bg'=>'rgba(16,185,129,.08)','border'=>'rgba(52,211,153,.2)'],
            ['route'=>'events.index',   'label'=>'All Events',    'sub'=>'View & manage events',    'icon'=>'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color'=>'#a78bfa','bg'=>'rgba(167,139,250,.1)','border'=>'rgba(167,139,250,.2)'],
            ['route'=>'profile.edit',   'label'=>'Profile',       'sub'=>'Account settings',        'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color'=>'#f472b6','bg'=>'rgba(244,114,182,.1)','border'=>'rgba(244,114,182,.2)'],
        ] as $qa)
        <a href="{{ route($qa['route']) }}"
           class="rounded-2xl p-4 border transition group"
           style="background:var(--panel);border-color:{{ $qa['border'] }}"
           onmouseover="this.style.background='var(--hover)'"
           onmouseout="this.style.background='var(--panel)'">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:{{ $qa['bg'] }}">
                <svg class="w-4 h-4" style="color:{{ $qa['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $qa['icon'] }}"/>
                </svg>
            </div>
            <p class="text-sm font-semibold" style="color:var(--text-strong)">{{ $qa['label'] }}</p>
            <p class="text-xs mt-0.5" style="color:var(--text-soft)">{{ $qa['sub'] }}</p>
        </a>
        @endforeach
    </div>

</div>
<style>
@keyframes pulse{0%,100%{opacity:.5}50%{opacity:1}}
</style>
</x-app-layout>