<x-app-layout>
<div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold" style="color:var(--text-strong)">Guest Book</h2>
            <p class="text-sm mt-1" style="color:var(--text-soft)">{{ $guests->total() }} guests total</p>
        </div>
        <a href="{{ route('guests.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl"
           style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Guest
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" class="rounded-2xl border p-4" style="background:var(--panel);border-color:var(--border)">
        <div class="flex gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-soft)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>
            <button type="submit"
                    class="px-5 py-2.5 text-white text-sm font-medium rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">Search</button>
            @if(request('search'))
            <a href="{{ route('guests.index') }}"
               class="px-5 py-2.5 text-sm font-medium rounded-xl"
               style="background:var(--input-bg);color:var(--text-soft)">Clear</a>
            @endif
        </div>
    </form>

    {{-- Guest table --}}
    <div class="rounded-2xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <table class="w-full text-sm">
            <thead style="border-bottom:1px solid var(--border)">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Events</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                <tr style="border-bottom:1px solid var(--border-soft)"
                    onmouseover="this.style.background='var(--hover)'"
                    onmouseout="this.style.background='transparent'">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                                {{ strtoupper(substr($guest->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium" style="color:var(--text-strong)">{{ $guest->name }}</p>
                                <p class="text-xs" style="color:var(--text-soft)">{{ $guest->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $guest->phone ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium"
                              style="background:rgba(99,102,241,.12);color:#818cf8;border:1px solid rgba(99,102,241,.2)">
                            {{ $guest->events_count ?? 0 }} events
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('guests.edit', $guest) }}"
                               class="text-xs font-medium transition hover:text-indigo-300" style="color:#818cf8">Edit</a>
                            <form method="POST" action="{{ route('guests.destroy', $guest) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($guest->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium transition hover:text-red-400" style="color:#f87171">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-14 text-center" style="color:var(--text-soft)">
                        No guests yet. <a href="{{ route('guests.create') }}" class="text-indigo-400 hover:underline">Add your first guest</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($guests->hasPages())
    <div class="flex justify-center">{{ $guests->links() }}</div>
    @endif
</div>
</x-app-layout>