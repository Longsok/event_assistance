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
