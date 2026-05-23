<x-admin-layout title="All Events">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">All Events</h2>
            @if(isset($events) && method_exists($events, 'total'))
            <p class="text-sm text-gray-500 mt-0.5">{{ $events->total() }} total</p>
            @endif
        </div>
        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </a>
    </div>

    <div class="rounded-xl border border-gray-800 overflow-hidden" style="background:#111827">
        <table class="w-full text-sm">
            <thead class="text-gray-400 uppercase text-xs border-b border-gray-800" style="background:#1f2937">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Organizer</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($events as $event)
                <tr class="hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-white font-medium">{{ $event->title }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->category->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $event->status === 'ongoing'   ? 'bg-emerald-900/40 text-emerald-400' :
                               ($event->status === 'completed' ? 'bg-blue-900/40 text-blue-400' :
                               ($event->status === 'draft'     ? 'bg-gray-800 text-gray-400' :
                                'bg-amber-900/40 text-amber-400')) }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">
                        {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.events.show', $event) }}"
                               class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">View</a>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                  onsubmit="return confirm('Delete event: {{ addslashes($event->title) }}? This will remove all tasks, guests, budget and schedules.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-400 hover:text-red-300 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">No events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($events, 'links'))
    <div class="mt-4">{{ $events->links() }}</div>
    @endif
</x-admin-layout>
