# Run from project root:
# powershell -ExecutionPolicy Bypass -File create_organizer_views.ps1

$base = "resources\views"

New-Item -ItemType Directory -Force -Path "$base\dashboard"
New-Item -ItemType Directory -Force -Path "$base\events"
New-Item -ItemType Directory -Force -Path "$base\guests"
New-Item -ItemType Directory -Force -Path "$base\tasks"
New-Item -ItemType Directory -Force -Path "$base\schedules"
New-Item -ItemType Directory -Force -Path "$base\budget"
New-Item -ItemType Directory -Force -Path "$base\contributions"
New-Item -ItemType Directory -Force -Path "$base\attendance"
New-Item -ItemType Directory -Force -Path "$base\invite"
New-Item -ItemType Directory -Force -Path "$base\public"
New-Item -ItemType Directory -Force -Path "$base\admin\events"

# ── dashboard/organizer.blade.php ─────────────────────────────────────────────
Set-Content "$base\dashboard\organizer.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label'=>'Total Events','value'=>$stats['total_events'],'color'=>'indigo'],
                ['label'=>'Total Guests','value'=>$stats['total_guests'],'color'=>'green'],
                ['label'=>'Overdue Tasks','value'=>$stats['overdue_tasks'],'color'=>'red'],
                ['label'=>'Contributions','value'=>'$'.number_format($stats['total_contributions'],2),'color'=>'yellow'],
            ] as $stat)
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <p class="text-sm text-gray-500">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stat['value'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Upcoming Events --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Upcoming Events</h3>
                    <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>
                @forelse($upcomingEvents as $event)
                <a href="{{ route('events.show', $event) }}"
                   class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded transition">
                    <div>
                        <p class="font-medium text-gray-800">{{ $event->title }}</p>
                        <p class="text-xs text-gray-400">{{ $event->start_date->format('M d, Y') }} · {{ $event->category->name ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $event->status === 'ongoing' ? 'bg-green-100 text-green-700' :
                               ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700') }}">
                            {{ $event->status }}
                        </span>
                        <p class="text-xs text-gray-400 mt-1">{{ $event->event_guests_count }} guests</p>
                    </div>
                </a>
                @empty
                <p class="text-gray-400 text-sm">No upcoming events.</p>
                @endforelse
                <div class="mt-4">
                    <a href="{{ route('events.create') }}"
                       class="w-full block text-center py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                        + Create New Event
                    </a>
                </div>
            </div>

            {{-- Overdue Tasks --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Overdue Tasks</h3>
                @forelse($overdueTasks as $task)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $task->task_name }}</p>
                        <p class="text-xs text-gray-400">{{ $task->event->title }} · {{ $task->group->name ?? '-' }}</p>
                    </div>
                    <span class="text-xs text-red-500">{{ $task->due_date->format('M d') }}</span>
                </div>
                @empty
                <p class="text-gray-400 text-sm">No overdue tasks 🎉</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Check-ins --}}
        @if($recentCheckIns->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Recent Check-ins</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @foreach($recentCheckIns as $log)
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold mx-auto mb-1">
                        {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                    </div>
                    <p class="text-xs font-medium text-gray-800 truncate">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $log->checked_in_at->format('H:i') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
'@

# ── events/index.blade.php ────────────────────────────────────────────────────
Set-Content "$base\events\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">My Events</h2>
            <a href="{{ route('events.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                + New Event
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" class="flex gap-3 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..."
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                <option value="">All Status</option>
                @foreach(['draft','published','ongoing','completed','archived'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Filter</button>
        </form>

        {{-- Events Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($events as $event)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span class="text-xs px-2 py-1 rounded-full
                            {{ $event->status === 'ongoing' ? 'bg-green-100 text-green-700' :
                               ($event->status === 'draft' ? 'bg-gray-100 text-gray-600' :
                               ($event->status === 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ ucfirst($event->status) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $event->category->name ?? '-' }}</span>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">{{ $event->title }}</h3>
                    <p class="text-sm text-gray-500 mb-3">
                        {{ $event->start_date->format('M d') }}
                        @if($event->start_date != $event->end_date) – {{ $event->end_date->format('M d, Y') }}
                        @else, {{ $event->start_date->format('Y') }}
                        @endif
                    </p>
                    <div class="flex items-center gap-4 text-xs text-gray-400 mb-4">
                        <span>👥 {{ $event->event_guests_count }} guests</span>
                        <span>📍 {{ $event->venue ?? 'No venue' }}</span>
                    </div>
                    <a href="{{ route('events.show', $event) }}"
                       class="block w-full text-center py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm rounded-lg transition font-medium">
                        Manage Event →
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-16 text-gray-400">
                <p class="text-lg mb-2">No events yet</p>
                <a href="{{ route('events.create') }}" class="text-indigo-600 hover:underline">Create your first event</a>
            </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    </div>
</x-app-layout>
'@

# ── events/create.blade.php ───────────────────────────────────────────────────
Set-Content "$base\events\create.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('events.index') }}" class="text-indigo-600 text-sm hover:underline">&larr; Back to Events</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Create New Event</h2>

            <form method="POST" action="{{ route('events.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                            <option value="">Select category...</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacity *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        @error('capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue Type *</label>
                        <select name="venue_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                            <option value="indoor" {{ old('venue_type') === 'indoor' ? 'selected' : '' }}>Indoor</option>
                            <option value="outdoor" {{ old('venue_type') === 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                            <option value="hybrid" {{ old('venue_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Budget ($)</label>
                        <input type="number" name="total_budget" value="{{ old('total_budget') }}" min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="meal_provided" id="meal" value="1" {{ old('meal_provided') ? 'checked' : '' }}>
                        <label for="meal" class="text-sm text-gray-700">Meal Provided</label>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium transition">
                    Create Event & Generate Timeline
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
'@

# ── events/edit.blade.php ─────────────────────────────────────────────────────
Set-Content "$base\events\edit.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; Back to Event</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit: {{ $event->title }}</h2>

            <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event Title *</label>
                        <input type="text" name="title" value="{{ old('title', $event->title) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $event->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue', $event->venue) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacity *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue Type *</label>
                        <select name="venue_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                            @foreach(['indoor','outdoor','hybrid'] as $vt)
                            <option value="{{ $vt }}" {{ old('venue_type', $event->venue_type) === $vt ? 'selected' : '' }}>{{ ucfirst($vt) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">{{ old('description', $event->description) }}</textarea>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium transition">
                    Update Event
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
'@

# ── events/show.blade.php ─────────────────────────────────────────────────────
Set-Content "$base\events\show.blade.php" @'
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
'@

# ── guests/index.blade.php ────────────────────────────────────────────────────
Set-Content "$base\guests\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Guest Book</h2>
            <a href="{{ route('guests.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                + Add Guest
            </a>
        </div>

        <form method="GET" class="mb-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
        </form>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Email</th>
                        <th class="px-5 py-3 text-left">Phone</th>
                        <th class="px-5 py-3 text-left">Events</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($guests as $guest)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $guest->name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $guest->email ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $guest->phone ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $guest->event_guests_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('guests.edit', $guest) }}" class="text-xs text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('guests.destroy', $guest) }}" onsubmit="return confirm('Delete this guest?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No guests yet. Add your first guest!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $guests->links() }}</div>
    </div>
</x-app-layout>
'@

# ── guests/create.blade.php ───────────────────────────────────────────────────
Set-Content "$base\guests\create.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-lg mx-auto">
        <div class="mb-6">
            <a href="{{ route('guests.index') }}" class="text-indigo-600 text-sm hover:underline">&larr; Guest Book</a>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Add New Guest</h2>
            <form method="POST" action="{{ route('guests.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Guest
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
'@

# ── guests/edit.blade.php ─────────────────────────────────────────────────────
Set-Content "$base\guests\edit.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-lg mx-auto">
        <div class="mb-6">
            <a href="{{ route('guests.index') }}" class="text-indigo-600 text-sm hover:underline">&larr; Guest Book</a>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Edit: {{ $guest->name }}</h2>
            <form method="POST" action="{{ route('guests.update', $guest) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $guest->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $guest->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $guest->phone) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Update Guest
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
'@

# ── guests/event-guests.blade.php ────────────────────────────────────────────
Set-Content "$base\guests\event-guests.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
                <h2 class="text-xl font-semibold text-gray-800 mt-1">Guest List</h2>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label'=>'Total','value'=>$stats['total'],'color'=>'gray'],
                ['label'=>'Confirmed','value'=>$stats['confirmed'],'color'=>'green'],
                ['label'=>'Pending','value'=>$stats['pending'],'color'=>'yellow'],
                ['label'=>'Declined','value'=>$stats['declined'],'color'=>'red'],
            ] as $s)
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $s['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Add guest --}}
        @if($availableGuests->count())
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Add Guest to Event</h3>
            <form method="POST" action="{{ route('events.guests.store', $event) }}" class="flex gap-3">
                @csrf
                <select name="guest_id" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                    <option value="">Select guest...</option>
                    @foreach($availableGuests as $g)
                    <option value="{{ $g->id }}">{{ $g->name }} {{ $g->email ? '('.$g->email.')' : '' }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                    Add
                </button>
            </form>
        </div>
        @endif

        {{-- Guest table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Guest</th>
                        <th class="px-5 py-3 text-left">Code</th>
                        <th class="px-5 py-3 text-left">RSVP</th>
                        <th class="px-5 py-3 text-left">Check-in</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($eventGuests as $eg)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $eg->guest->name }}</p>
                            <p class="text-xs text-gray-400">{{ $eg->guest->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $eg->guest_code ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs
                                {{ $eg->rsvp_status === 'confirmed' ? 'bg-green-100 text-green-700' :
                                   ($eg->rsvp_status === 'declined' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $eg->rsvp_status }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($eg->isCheckedIn())
                                <span class="text-green-600 text-xs font-medium">✅ Checked in</span>
                            @else
                                <span class="text-gray-400 text-xs">Not yet</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('events.guests.destroy', [$event, $eg]) }}"
                                  onsubmit="return confirm('Remove this guest?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No guests added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $eventGuests->links() }}</div>
    </div>
</x-app-layout>
'@

# ── tasks/index.blade.php ─────────────────────────────────────────────────────
Set-Content "$base\tasks\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
                <h2 class="text-xl font-semibold text-gray-800 mt-1">Task Checklist</h2>
            </div>
            <div class="text-sm text-gray-500">
                {{ $progress['completed'] }} / {{ $progress['total'] }} completed
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="bg-gray-200 rounded-full h-2">
            @php $pct = $progress['total'] > 0 ? round(($progress['completed']/$progress['total'])*100) : 0; @endphp
            <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
        </div>

        {{-- Task Groups --}}
        @foreach($groups as $group)
        @if($group->eventTasks->count())
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                <div class="w-3 h-3 rounded-full" style="background: {{ $group->color }}"></div>
                <h3 class="font-semibold text-gray-800">{{ $group->name }}</h3>
                <span class="text-xs text-gray-400 ml-auto">{{ $group->eventTasks->count() }} tasks</span>
            </div>
            @foreach($group->eventTasks as $task)
            <div class="flex items-center gap-4 px-5 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                <div class="flex-shrink-0">
                    @if($task->status === 'done')
                        <form method="POST" action="{{ route('events.tasks.reopen', [$event, $task]) }}">
                            @csrf @method('PATCH')
                            <button class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center text-white text-xs">✓</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('events.tasks.complete', [$event, $task]) }}">
                            @csrf @method('PATCH')
                            <button class="w-5 h-5 rounded-full border-2 border-gray-300 hover:border-indigo-500 transition"></button>
                        </form>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">
                        {{ $task->task_name }}
                    </p>
                    @if($task->notes)
                    <p class="text-xs text-gray-400 truncate">{{ $task->notes }}</p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs px-2 py-1 rounded
                        {{ $task->priority === 'high' ? 'bg-red-100 text-red-600' :
                           ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-500') }}">
                        {{ $task->priority }}
                    </span>
                    <p class="text-xs mt-1 {{ $task->status === 'overdue' ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                        {{ $task->due_date->format('M d') }}
                    </p>
                </div>
                <form method="POST" action="{{ route('events.tasks.destroy', [$event, $task]) }}"
                      onsubmit="return confirm('Delete task?')">
                    @csrf @method('DELETE')
                    <button class="text-gray-300 hover:text-red-400 text-xs">✕</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
        @endforeach
    </div>
</x-app-layout>
'@

# ── schedules/index.blade.php ─────────────────────────────────────────────────
Set-Content "$base\schedules\index.blade.php" @'
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
'@

# ── budget/index.blade.php ────────────────────────────────────────────────────
Set-Content "$base\budget\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Budget Tracker</h2>
        </div>

        {{-- Total Budget --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <form method="POST" action="{{ route('events.budget.update', $event) }}" class="flex items-end gap-4">
                @csrf @method('PATCH')
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Budget ($)</label>
                    <input type="number" name="total_budget" value="{{ $budget?->total_budget ?? 0 }}" min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                    Update
                </button>
            </form>
        </div>

        @if($budget)
        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-gray-900">${{ number_format($budget->total_budget, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Budget</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-gray-900">${{ number_format($budget->total_allocated, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Allocated</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-gray-900">${{ number_format($budget->total_actual, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Spent</p>
            </div>
        </div>

        {{-- Budget Items --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Item</th>
                        <th class="px-5 py-3 text-right">Allocated</th>
                        <th class="px-5 py-3 text-right">Actual</th>
                        <th class="px-5 py-3 text-right">Remaining</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($budget->items as $item)
                    <tr class="hover:bg-gray-50 {{ $item->isOverBudget() ? 'bg-red-50' : '' }}">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->line_item }}</td>
                        <td class="px-5 py-3 text-right text-gray-600">${{ number_format($item->allocated_amount, 2) }}</td>
                        <td class="px-5 py-3 text-right {{ $item->isOverBudget() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            ${{ number_format($item->actual_amount, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right {{ $item->remaining < 0 ? 'text-red-500' : 'text-green-600' }}">
                            ${{ number_format($item->remaining, 2) }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('events.budget.items.destroy', [$event, $item]) }}"
                                  onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</x-app-layout>
'@

# ── contributions/index.blade.php ─────────────────────────────────────────────
Set-Content "$base\contributions\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Contributions</h2>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-green-600">${{ number_format($stats['total_received'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Received</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-yellow-600">${{ number_format($stats['total_pending'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Pending</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-gray-900">{{ $stats['count'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Records</p>
            </div>
        </div>

        {{-- Add contribution --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Record Contribution</h3>
            <form method="POST" action="{{ route('events.contributions.store', $event) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Guest *</label>
                    <select name="guest_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        <option value="">Select guest...</option>
                        @foreach($eventGuests as $eg)
                        <option value="{{ $eg->guest_id }}">{{ $eg->guest->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Amount *</label>
                    <input type="number" name="amount" min="0.01" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Method *</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status *</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        <option value="received">Received</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Reference #</label>
                    <input type="text" name="reference_number"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                        Record
                    </button>
                </div>
            </form>
        </div>

        {{-- List --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Guest</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-left">Method</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contributions as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $c->guest->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-right text-gray-800">${{ number_format($c->amount, 2) }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $c->payment_method }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $c->status === 'received' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $c->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('events.contributions.destroy', [$event, $c]) }}"
                                  onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No contributions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
'@

# ── attendance/index.blade.php ────────────────────────────────────────────────
Set-Content "$base\attendance\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Attendance</h2>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['checked_in'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Checked In</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $stats['expected'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Expected (Confirmed)</p>
            </div>
        </div>

        {{-- QR Code / Controls --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            @if($event->attendance_token)
                <div class="text-center mb-4">
                    <p class="text-sm text-green-600 font-medium mb-3">✅ Check-in is ACTIVE</p>
                    @if($qrCode)
                    <div class="inline-block p-3 bg-white border-2 border-gray-200 rounded-xl">
                        {!! $qrCode !!}
                    </div>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">Guests scan this QR to check in</p>
                </div>
                <form method="POST" action="{{ route('events.attendance.stop', $event) }}">
                    @csrf
                    <button class="w-full py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition">
                        Stop Check-in
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500 mb-4 text-center">Check-in is not active. Start it to generate the QR code.</p>
                <form method="POST" action="{{ route('events.attendance.start', $event) }}">
                    @csrf
                    <button class="w-full py-2 bg-green-600 hover:bg-green-500 text-white text-sm rounded-lg transition">
                        Start Check-in
                    </button>
                </form>
            @endif
        </div>

        {{-- Check-in Log --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Check-in Log</h3>
            </div>
            @forelse($checkedIn as $log)
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 text-xs font-bold">
                        {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400">{{ $log->scan_method }} scan</p>
                    </div>
                </div>
                <span class="text-sm text-gray-500">{{ $log->checked_in_at->format('H:i') }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400">No check-ins yet.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
'@

# ── invite/show.blade.php ─────────────────────────────────────────────────────
Set-Content "$base\invite\show.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Invite Card</h2>
        </div>

        {{-- Invite Link --}}
        <div class="bg-indigo-50 rounded-xl border border-indigo-100 p-5">
            <p class="text-sm font-medium text-indigo-800 mb-2">Public Invite Link</p>
            <div class="flex gap-2">
                <input type="text" value="{{ $inviteUrl }}" readonly
                       class="flex-1 bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm text-gray-700"
                       onclick="this.select()">
                <button onclick="navigator.clipboard.writeText('{{ $inviteUrl }}');alert('Copied!')"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                    Copy
                </button>
            </div>
            <p class="text-xs text-indigo-600 mt-2">Share this link with guests to let them self-register.</p>
        </div>

        {{-- Settings --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Invite Card Settings</h3>
            <form method="POST" action="{{ route('events.invite.update', $event) }}" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Style</label>
                    <select name="template_style" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        @foreach(['default','elegant','minimal'] as $style)
                        <option value="{{ $style }}" {{ ($inviteCard?->template_style ?? 'default') === $style ? 'selected' : '' }}>
                            {{ ucfirst($style) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    @foreach(['show_agenda'=>'Show Agenda','show_venue'=>'Show Venue','show_qr'=>'Show QR Code'] as $field => $label)
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1"
                               {{ $inviteCard?->$field ? 'checked' : '' }}>
                        <label for="{{ $field }}" class="text-sm text-gray-700">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Message</label>
                    <textarea name="custom_message" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">{{ $inviteCard?->custom_message }}</textarea>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Save Settings
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
'@

# ── events/complete.blade.php ─────────────────────────────────────────────────
Set-Content "$base\events\complete.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Complete Event</h2>
        </div>

        @if($completion)
        {{-- Show summary --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 text-lg">Event Summary</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Attendance</p>
                    <p class="font-bold text-gray-900 text-lg">{{ $completion->total_attended }}/{{ $completion->total_invited }}</p>
                    <p class="text-xs text-gray-400">{{ $completion->attendance_rate }}% rate</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Tasks</p>
                    <p class="font-bold text-gray-900 text-lg">{{ $completion->tasks_completed }}/{{ $completion->tasks_total }}</p>
                    <p class="text-xs text-gray-400">{{ $completion->task_completion_rate }}% complete</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Budget Spent</p>
                    <p class="font-bold text-gray-900 text-lg">${{ number_format($completion->total_spent, 2) }}</p>
                    <p class="text-xs text-gray-400">of ${{ number_format($completion->total_budget, 2) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Contributions</p>
                    <p class="font-bold text-gray-900 text-lg">${{ number_format($completion->total_contributions, 2) }}</p>
                    <p class="text-xs text-gray-400">received</p>
                </div>
            </div>
            @if($completion->organizer_notes)
            <div class="bg-yellow-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $completion->organizer_notes }}</p>
            </div>
            @endif
        </div>
        @else
        {{-- Complete form --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-gray-600 text-sm mb-6">Mark this event as completed. This will generate a final summary report.</p>
            <form method="POST" action="{{ route('events.completion.store', $event) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organizer Notes (optional)</label>
                    <textarea name="organizer_notes" rows="4" placeholder="How did the event go? Any notes for next time..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">{{ old('organizer_notes') }}</textarea>
                </div>
                <button type="submit"
                        onclick="return confirm('Mark this event as completed?')"
                        class="w-full py-3 bg-green-600 hover:bg-green-500 text-white rounded-lg font-medium transition">
                    ✅ Mark as Completed
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
'@

# ── public/register.blade.php ─────────────────────────────────────────────────
Set-Content "$base\public\register.blade.php" @'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-indigo-600 px-6 py-8 text-white text-center">
                <h1 class="text-2xl font-bold">{{ $event->title }}</h1>
                <p class="text-indigo-200 mt-1">{{ $event->start_date->format('M d, Y') }}</p>
                @if($event->venue)<p class="text-indigo-200 text-sm">📍 {{ $event->venue }}</p>@endif
            </div>

            <div class="p-6">
                @if(isset($isFull) && $isFull)
                    <div class="text-center py-4">
                        <p class="text-red-500 font-medium">Registration is full.</p>
                    </div>
                @elseif(isset($registered) && $registered)
                    <div class="text-center py-4 space-y-3">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-3xl">✅</span>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-800">You're registered!</h2>
                        <p class="text-gray-500 text-sm">Welcome, {{ $guestName }}</p>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 mb-1">Your Guest Code</p>
                            <p class="text-2xl font-mono font-bold text-indigo-700">{{ $guestCode }}</p>
                            <p class="text-xs text-gray-400 mt-1">Keep this code — you'll need it to check in</p>
                        </div>
                    </div>
                @elseif(isset($alreadyJoined) && $alreadyJoined)
                    <div class="text-center py-4 space-y-3">
                        <p class="text-yellow-600 font-medium">You're already registered!</p>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 mb-1">Your Guest Code</p>
                            <p class="text-2xl font-mono font-bold text-indigo-700">{{ $guestCode }}</p>
                        </div>
                    </div>
                @else
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Register for this event</h2>
                    <form method="POST" action="{{ route('public.register.store', $event->invite_token) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        </div>
                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-medium transition">
                            Register Now
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
'@

# ── public/checkin.blade.php ──────────────────────────────────────────────────
Set-Content "$base\public\checkin.blade.php" @'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check In — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-green-600 px-6 py-8 text-white text-center">
                <h1 class="text-2xl font-bold">Check In</h1>
                <p class="text-green-200 mt-1">{{ $event->title }}</p>
            </div>
            <div class="p-6">
                @if(isset($alreadyCheckedIn) && $alreadyCheckedIn)
                    <div class="text-center py-4 space-y-3">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                            <span class="text-3xl">⚠️</span>
                        </div>
                        <p class="font-semibold text-gray-800">Already Checked In</p>
                        <p class="text-gray-500 text-sm">{{ $guestName }} checked in at {{ $checkedInAt->format('H:i') }}</p>
                    </div>
                @else
                    <p class="text-gray-600 text-sm mb-5">Enter your guest code and name to check in.</p>
                    <form method="POST" action="{{ route('public.checkin.store', $event->attendance_token) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Guest Code *</label>
                            <input type="text" name="guest_code" value="{{ old('guest_code') }}" placeholder="e.g. WEDD-2026-001"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-green-400 uppercase" required>
                            @error('guest_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-green-400" required>
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-500 text-white rounded-lg font-medium transition">
                            Check In
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
'@

# ── public/checkin-success.blade.php ─────────────────────────────────────────
Set-Content "$base\public\checkin-success.blade.php" @'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-green-600 px-6 py-8 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-4xl">✅</span>
                </div>
                <h1 class="text-2xl font-bold">Welcome, {{ $guestName }}!</h1>
                <p class="text-green-200 mt-1">Checked in at {{ $checkedInAt->format('H:i') }}</p>
            </div>
            <div class="p-6">
                <h2 class="font-semibold text-gray-800 mb-4">{{ $event->title }}</h2>
                @if($todaySchedule->count())
                <h3 class="text-sm font-medium text-gray-600 mb-3">Today's Program</h3>
                <div class="space-y-2">
                    @foreach($todaySchedule as $session)
                    <div class="flex gap-3 py-2 border-b border-gray-100 last:border-0">
                        <span class="text-xs text-gray-400 font-mono w-20 flex-shrink-0">{{ $session->start_time }}</span>
                        <span class="text-sm text-gray-700 {{ $session->is_break ? 'italic text-gray-400' : '' }}">
                            {{ $session->session_name }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
                <p class="text-center text-gray-400 text-sm mt-6">Enjoy the event! 🎉</p>
            </div>
        </div>
    </div>
</body>
</html>
'@

# ── admin/events/show.blade.php ───────────────────────────────────────────────
Set-Content "$base\admin\events\show.blade.php" @'
<x-admin-layout>
    <x-slot name="title">Event: {{ $event->title }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.events.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; All Events</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-gray-900 rounded-xl border border-gray-800 p-6 space-y-4">
            <div class="flex items-start justify-between">
                <h2 class="text-xl font-semibold text-white">{{ $event->title }}</h2>
                <span class="px-2 py-1 rounded text-xs
                    {{ $event->status === 'ongoing' ? 'bg-green-900/40 text-green-400' : 'bg-gray-800 text-gray-400' }}">
                    {{ ucfirst($event->status) }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-500">Category</p><p class="text-white">{{ $event->category->name ?? '-' }}</p></div>
                <div><p class="text-gray-500">Organizer</p><p class="text-white">{{ $event->user->name ?? '-' }}</p></div>
                <div><p class="text-gray-500">Date</p><p class="text-white">{{ $event->start_date->format('M d') }} – {{ $event->end_date->format('M d, Y') }}</p></div>
                <div><p class="text-gray-500">Venue</p><p class="text-white">{{ $event->venue ?? '-' }}</p></div>
                <div><p class="text-gray-500">Capacity</p><p class="text-white">{{ $event->capacity }}</p></div>
                <div><p class="text-gray-500">Guests</p><p class="text-white">{{ $event->eventGuests->count() }}</p></div>
            </div>
            @if($event->description)
            <div>
                <p class="text-gray-500 text-sm mb-1">Description</p>
                <p class="text-gray-300 text-sm">{{ $event->description }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-gray-900 rounded-xl border border-gray-800 p-5">
                <h3 class="text-white font-semibold mb-3">Stats</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Tasks</span><span class="text-white">{{ $event->tasks->count() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Schedules</span><span class="text-white">{{ $event->schedules->count() }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Budget</span><span class="text-white">${{ number_format($event->budget?->total_budget ?? 0, 0) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
'@

Write-Host ""
Write-Host "✅ All organizer and public views created!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Replace routes/web.php with the new web.php file"
Write-Host "2. Run: php artisan optimize:clear"
Write-Host "3. Visit http://localhost:8000/dashboard"
