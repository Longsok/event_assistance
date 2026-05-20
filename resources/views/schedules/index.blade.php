<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Event Schedule</h2>
        </div>

        @foreach($schedulesByDay as $day => $sessions)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 bg-indigo-50 border-b border-indigo-100">
                <h3 class="font-semibold text-indigo-800">
                    Day {{ $day }} — {{ $event->start_date->copy()->addDays($day - 1)->format('l, M d') }}
                </h3>
            </div>
            @foreach($sessions as $session)
            <div class="flex items-start gap-4 px-5 py-4 border-b border-gray-100 last:border-0 {{ $session->is_break ? 'bg-gray-50' : '' }}">
                <div class="text-sm text-gray-500 w-24 flex-shrink-0 font-mono">
                    {{ $session->start_time }} – {{ $session->end_time }}
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800 {{ $session->is_break ? 'text-gray-500 italic' : '' }}">
                        {{ $session->session_name }}
                        @if($session->is_break) <span class="text-xs">(Break)</span> @endif
                    </p>
                    @if($session->location)
                    <p class="text-xs text-gray-400 mt-0.5">📍 {{ $session->location }}</p>
                    @endif
                </div>
                <span class="text-xs text-gray-400">{{ $session->duration_minutes }}m</span>
                <form method="POST" action="{{ route('events.schedule.destroy', [$event, $session]) }}"
                      onsubmit="return confirm('Delete session?')">
                    @csrf @method('DELETE')
                    <button class="text-gray-300 hover:text-red-400 text-xs">✕</button>
                </form>
            </div>
            @endforeach
        </div>
        @endforeach

        @if($schedulesByDay->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
            No schedule yet.
        </div>
        @endif
    </div>
</x-app-layout>
