<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-xl font-semibold text-slate-900 mt-1">Event Schedule</h2>
    </div>
    @foreach($schedulesByDay as $day => $sessions)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-indigo-50 border-b border-indigo-100">
            <h3 class="font-semibold text-indigo-800">
                Day {{ $day }} — {{ $event->start_date->copy()->addDays($day - 1)->format('l, M d') }}
            </h3>
        </div>
        @foreach($sessions as $session)
        <div class="flex items-start gap-4 px-5 py-4 border-b border-slate-100 last:border-0 {{ $session->is_break ?? false ? 'bg-slate-50' : '' }}">
            <div class="text-sm text-slate-500 w-28 flex-shrink-0 font-mono">
                {{ $session->start_time }} – {{ $session->end_time }}
            </div>
            <div class="flex-1">
                <p class="font-medium text-slate-800">{{ $session->session_name }}</p>
                @if($session->speaker)
                <p class="text-xs text-slate-400 mt-0.5">{{ $session->speaker }}</p>
                @endif
                @if($session->location)
                <p class="text-xs text-slate-400 mt-0.5">{{ $session->location }}</p>
                @endif
            </div>
            <form method="POST" action="{{ route('events.schedule.destroy', [$event, $session]) }}" onsubmit="return confirm('Delete session?')">
                @csrf @method('DELETE')
                <button class="text-slate-300 hover:text-red-400 text-xs p-1 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endforeach
    @if($schedulesByDay->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-16 text-center text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        No schedule yet.
    </div>
    @endif
</div>
</x-app-layout>
