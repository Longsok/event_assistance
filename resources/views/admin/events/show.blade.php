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
