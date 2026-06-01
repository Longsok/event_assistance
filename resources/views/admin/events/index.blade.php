<x-admin-layout title="All Events">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold" style="color:var(--text-strong)">All Events</h2>
            @if(isset($events) && method_exists($events, 'total'))
            <p class="text-sm mt-0.5" style="color:var(--text-soft)">{{ $events->total() }} total</p>
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

    <div class="rounded-xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <table class="w-full text-sm">
            <thead class="uppercase text-xs" style="background:var(--panel-input);color:var(--text-soft);border-bottom:1px solid var(--border)">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Organizer</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr style="border-bottom:1px solid var(--border)"
                    onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                    <td class="px-6 py-4 font-medium" style="color:var(--text-strong)">{{ $event->title }}</td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $event->user->name ?? '-' }}</td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $event->category->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $event->status === 'ongoing'   ? 'bg-emerald-900/40 text-emerald-400' :
                               ($event->status === 'completed' ? 'bg-blue-900/40 text-blue-400' :
                               ($event->status === 'draft'     ? 'bg-gray-800 text-gray-400' :
                                'bg-amber-900/40 text-amber-400')) }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">
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
                    <td colspan="6" class="px-6 py-8 text-center" style="color:var(--text-soft)">No events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($events, 'links'))
    <div class="mt-4">{{ $events->links() }}</div>
    @endif
</x-admin-layout>