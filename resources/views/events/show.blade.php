<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('events.index') }}" class="text-indigo-600 text-sm hover:underline">&larr; My Events</a>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $event->title }}</h1>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $event->start_date->format('M d') }} – {{ $event->end_date->format('M d, Y') }}
                    @if($event->venue) · {{ $event->venue }} @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $event->status === 'ongoing' ? 'bg-green-100 text-green-700' :
                       ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' :
                       ($event->status === 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                    {{ ucfirst($event->status) }}
                </span>
                <a href="{{ route('events.edit', $event) }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                    Edit
                </a>
            </div>
        </div>

        {{-- Quick Nav --}}
        <div class="flex flex-wrap gap-2">
            @foreach([
                ['label'=>'Tasks','route'=>'events.tasks.index','icon'=>'✅'],
                ['label'=>'Guests','route'=>'events.guests.index','icon'=>'👥'],
                ['label'=>'Schedule','route'=>'events.schedule.index','icon'=>'📅'],
                ['label'=>'Budget','route'=>'events.budget.index','icon'=>'💰'],
                ['label'=>'Contributions','route'=>'events.contributions.index','icon'=>'💳'],
                ['label'=>'Attendance','route'=>'events.attendance.index','icon'=>'📱'],
                ['label'=>'Invite Card','route'=>'events.invite.show','icon'=>'🎟️'],
            ] as $nav)
            <a href="{{ route($nav['route'], $event) }}"
               class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700 transition">
                {{ $nav['icon'] }} {{ $nav['label'] }}
            </a>
            @endforeach
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $event->eventGuests->count() }}</p>
                <p class="text-xs text-gray-500 mt-1">Guests</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $event->tasks->where('status','done')->count() }}/{{ $event->tasks->count() }}</p>
                <p class="text-xs text-gray-500 mt-1">Tasks Done</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">${{ number_format($event->budget?->total_budget ?? 0, 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Budget</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $event->schedules->count() }}</p>
                <p class="text-xs text-gray-500 mt-1">Sessions</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Tasks --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Tasks</h3>
                    <a href="{{ route('events.tasks.index', $event) }}" class="text-indigo-600 text-xs hover:underline">View all</a>
                </div>
                @forelse($event->tasks->take(5) as $task)
                <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                    <div class="w-4 h-4 rounded-full border-2 {{ $task->status === 'done' ? 'bg-green-500 border-green-500' : 'border-gray-300' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 truncate {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">{{ $task->task_name }}</p>
                    </div>
                    <span class="text-xs {{ $task->status === 'overdue' ? 'text-red-500' : 'text-gray-400' }}">
                        {{ $task->due_date->format('M d') }}
                    </span>
                </div>
                @empty
                <p class="text-gray-400 text-sm">No tasks yet.</p>
                @endforelse
            </div>

            {{-- Recent Guests --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Guests</h3>
                    <a href="{{ route('events.guests.index', $event) }}" class="text-indigo-600 text-xs hover:underline">View all</a>
                </div>
                @forelse($event->eventGuests->take(5) as $eg)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                            {{ strtoupper(substr($eg->guest->name ?? '?', 0, 1)) }}
                        </div>
                        <span class="text-sm text-gray-800">{{ $eg->guest->name ?? '-' }}</span>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $eg->rsvp_status === 'confirmed' ? 'bg-green-100 text-green-700' :
                           ($eg->rsvp_status === 'declined' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ $eg->rsvp_status }}
                    </span>
                </div>
                @empty
                <p class="text-gray-400 text-sm">No guests yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Complete Event Button --}}
        @if(in_array($event->status, ['ongoing','published']))
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
            <div>
                <p class="font-medium text-gray-800">Mark Event as Completed</p>
                <p class="text-sm text-gray-500">Generate the final summary report for this event.</p>
            </div>
            <a href="{{ route('events.completion.show', $event) }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white text-sm rounded-lg transition">
                Complete Event
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
