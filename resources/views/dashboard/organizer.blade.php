<x-app-layout>
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}
            </h1>
            <p class="text-slate-500 text-sm mt-1">Here is what is happening with your events today.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Total Events','value'=>$stats['total_events'],'color'=>'text-slate-900','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label'=>'Total Guests','value'=>$stats['total_guests'],'color'=>'text-slate-900','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label'=>'Overdue Tasks','value'=>$stats['overdue_tasks'],'color'=>'text-red-600','icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Contributions','value'=>'$'.number_format($stats['total_contributions'],0),'color'=>'text-emerald-600','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $s)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $s['icon'] }}"/></svg>
            </div>
            <p class="text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Upcoming Events</h3>
                <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
            </div>
            @forelse($upcomingEvents as $event)
            <a href="{{ route('events.show', $event) }}" class="flex items-center gap-4 px-6 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex flex-col items-center justify-center flex-shrink-0 border border-indigo-100">
                    <span class="text-xs font-bold text-indigo-600 leading-none">{{ $event->start_date->format('M') }}</span>
                    <span class="text-lg font-bold text-indigo-900 leading-none">{{ $event->start_date->format('d') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-slate-800 truncate">{{ $event->title }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $event->category->name ?? 'No category' }} &middot; {{ $event->event_guests_count ?? 0 }} guests</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0
                    {{ $event->status==='ongoing' ? 'bg-emerald-100 text-emerald-700' : ($event->status==='draft' ? 'bg-slate-100 text-slate-600' : ($event->status==='completed' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')) }}">
                    {{ ucfirst($event->status) }}
                </span>
            </a>
            @empty
            <div class="px-6 py-12 text-center">
                <p class="text-slate-400 text-sm mb-4">No upcoming events yet.</p>
                <a href="{{ route('events.create') }}" class="inline-block px-4 py-2 text-white text-sm rounded-xl bg-indigo-600 hover:bg-indigo-500 transition">Create first event</a>
            </div>
            @endforelse
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Overdue Tasks</h3>
            </div>
            @forelse($overdueTasks as $task)
            <div class="px-5 py-3 border-b border-slate-100 last:border-0">
                <p class="text-sm font-medium text-slate-800 truncate">{{ $task->task_name }}</p>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-xs text-slate-400 truncate">{{ $task->event->title }}</p>
                    <span class="text-xs text-red-500 font-medium flex-shrink-0 ml-2">{{ $task->due_date->format('M d') }}</span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <p class="text-slate-400 text-sm">No overdue tasks.</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($recentCheckIns->count())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Recent Check-ins</h3>
        </div>
        <div class="px-6 py-4 flex flex-wrap gap-3">
            @foreach($recentCheckIns as $log)
            <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2">
                <div class="w-7 h-7 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-800">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                    <p class="text-xs text-slate-400">{{ $log->checked_in_at->format('H:i') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
</x-app-layout>
