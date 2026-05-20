<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

        {{-- Welcome --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">Here's what's happening with your events today.</p>
            </div>
            <a href="{{ route('events.create') }}"
               class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-medium rounded-xl transition shadow-sm"
               style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                + New Event
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_events'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Events</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_guests'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Guests</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-2xl font-bold text-red-600">{{ $stats['overdue_tasks'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Overdue Tasks</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_contributions'], 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">Contributions</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Upcoming Events --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Upcoming Events</h3>
                    <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>
                @forelse($upcomingEvents as $event)
                <a href="{{ route('events.show', $event) }}"
                   class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 flex flex-col items-center justify-center flex-shrink-0 border border-indigo-100">
                        <span class="text-xs font-bold text-indigo-600 leading-none">{{ $event->start_date->format('M') }}</span>
                        <span class="text-lg font-bold text-indigo-900 leading-none">{{ $event->start_date->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 truncate">{{ $event->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $event->category->name ?? 'No category' }} &middot; {{ $event->event_guests_count }} guests</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium flex-shrink-0
                        {{ $event->status === 'ongoing'   ? 'bg-green-100 text-green-700' :
                           ($event->status === 'draft'    ? 'bg-gray-100 text-gray-600' :
                           ($event->status === 'completed'? 'bg-blue-100 text-blue-700' :
                            'bg-yellow-100 text-yellow-700')) }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </a>
                @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-gray-400 text-sm mb-4">No upcoming events yet.</p>
                    <a href="{{ route('events.create') }}"
                       class="inline-block px-4 py-2 text-white text-sm rounded-xl transition"
                       style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
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
                        <span class="text-xs text-red-500 font-medium flex-shrink-0 ml-2">
                            {{ $task->due_date->format('M d') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <p class="text-gray-400 text-sm">No overdue tasks.</p>
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
