<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                My Events
            </a>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $event->title }}</h1>
            <p class="text-slate-500 text-sm mt-1">
                {{ $event->start_date->format('M d') }} – {{ $event->end_date->format('M d, Y') }}
                @if($event->venue) &middot; {{ $event->venue }} @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-sm font-medium
                {{ $event->status==='ongoing' ? 'bg-emerald-100 text-emerald-700' : ($event->status==='draft' ? 'bg-slate-100 text-slate-600' : ($event->status==='completed' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')) }}">
                {{ ucfirst($event->status) }}
            </span>
            <a href="{{ route('events.edit', $event) }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm rounded-xl hover:bg-slate-50 transition">Edit</a>
        </div>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach([
            ['label'=>'Tasks','route'=>'events.tasks.index','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['label'=>'Guests','route'=>'events.guests.index','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label'=>'Schedule','route'=>'events.schedule.index','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label'=>'Budget','route'=>'events.budget.index','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Contributions','route'=>'events.contributions.index','icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ['label'=>'Attendance','route'=>'events.attendance.index','icon'=>'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z'],
            ['label'=>'Invite Card','route'=>'events.invite.show','icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ] as $nav)
        <a href="{{ route($nav['route'], $event) }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $nav['icon'] }}"/></svg>
            {{ $nav['label'] }}
        </a>
        @endforeach
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Guests','value'=>$event->eventGuests->count(),'color'=>'text-slate-900'],
            ['label'=>'Tasks Done','value'=>$event->tasks->where('status','done')->count().'/'.$event->tasks->count(),'color'=>'text-indigo-600'],
            ['label'=>'Total Budget','value'=>'$'.number_format($event->budget?->total_budget ?? 0,0),'color'=>'text-slate-900'],
            ['label'=>'Sessions','value'=>$event->schedules->count(),'color'=>'text-slate-900'],
        ] as $s)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-slate-800">Recent Tasks</h3>
                <a href="{{ route('events.tasks.index', $event) }}" class="text-indigo-600 text-xs hover:underline">View all</a>
            </div>
            @forelse($event->tasks->take(5) as $task)
            <div class="flex items-center gap-3 py-2.5 border-b border-slate-100 last:border-0">
                <div class="w-4 h-4 rounded-full border-2 flex-shrink-0 {{ $task->status==='done' ? 'bg-emerald-500 border-emerald-500' : ($task->status==='overdue' ? 'border-red-400' : 'border-slate-300') }}"></div>
                <p class="flex-1 text-sm text-slate-800 truncate {{ $task->status==='done' ? 'line-through text-slate-400' : '' }}">{{ $task->task_name }}</p>
                <span class="text-xs {{ $task->status==='overdue' ? 'text-red-500' : 'text-slate-400' }}">{{ $task->due_date->format('M d') }}</span>
            </div>
            @empty
            <p class="text-slate-400 text-sm">No tasks yet.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-slate-800">Guest List</h3>
                <a href="{{ route('events.guests.index', $event) }}" class="text-indigo-600 text-xs hover:underline">View all</a>
            </div>
            @forelse($event->eventGuests->take(5) as $eg)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                        {{ strtoupper(substr($eg->guest->name ?? '?', 0, 1)) }}
                    </div>
                    <span class="text-sm text-slate-800">{{ $eg->guest->name ?? '-' }}</span>
                </div>
                <span class="text-xs px-2 py-1 rounded-full {{ $eg->rsvp_status==='confirmed' ? 'bg-emerald-100 text-emerald-700' : ($eg->rsvp_status==='declined' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600') }}">
                    {{ $eg->rsvp_status }}
                </span>
            </div>
            @empty
            <p class="text-slate-400 text-sm">No guests yet.</p>
            @endforelse
        </div>
    </div>

    @if(in_array($event->status, ['ongoing','published']))
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
        <div>
            <p class="font-semibold text-slate-800">Mark Event as Completed</p>
            <p class="text-sm text-slate-500">Generate the final summary report for this event.</p>
        </div>
        <a href="{{ route('events.completion.show', $event) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm rounded-xl transition">Complete Event</a>
    </div>
    @endif
</div>
</x-app-layout>
