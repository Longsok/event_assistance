<x-app-layout>
<div class="fixed inset-0 pointer-events-none" style="z-index:0;background-image:linear-gradient(rgba(255,255,255,.015) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.015) 1px,transparent 1px);background-size:48px 48px"></div>
<div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">My Events</h2>
            <p class="text-sm mt-1" style="color:#6b7280">{{ $events->total() }} events total</p>
        </div>
        <a href="{{ route('events.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition"
           style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 12px rgba(79,70,229,.35)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Event
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="rounded-2xl border p-4" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#6b7280" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            </div>
            <select name="status"
                    class="rounded-xl px-3 py-2.5 text-sm focus:outline-none text-white"
                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                <option value="">All Status</option>
                @foreach(['draft','published','ongoing','completed','archived'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }} style="background:#0d1117">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="px-5 py-2.5 text-white text-sm font-medium rounded-xl transition"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">Search</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('events.index') }}"
               class="px-5 py-2.5 text-sm font-medium rounded-xl transition"
               style="background:rgba(255,255,255,.06);color:#9ca3af">Clear</a>
            @endif
        </div>
    </form>

    {{-- Events grid --}}
    @if($events->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($events as $event)
        <div class="rounded-2xl border overflow-hidden group transition"
             style="background:#0d1117;border-color:rgba(255,255,255,.07)"
             onmouseover="this.style.borderColor='rgba(99,102,241,.4)'"
             onmouseout="this.style.borderColor='rgba(255,255,255,.07)'">

            {{-- Status bar --}}
            <div class="h-1 w-full {{ $event->status==='ongoing' ? 'bg-emerald-500' : ($event->status==='completed' ? 'bg-blue-500' : ($event->status==='draft' ? 'bg-gray-700' : 'bg-indigo-500')) }}"></div>

            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        {{ $event->status==='ongoing' ? 'bg-emerald-900/40 text-emerald-400' :
                           ($event->status==='draft' ? 'text-gray-400' :
                           ($event->status==='completed' ? 'bg-blue-900/40 text-blue-400' :
                            'bg-indigo-900/40 text-indigo-300')) }}"
                        style="{{ $event->status==='draft' ? 'background:rgba(255,255,255,.07)' : '' }}">
                        {{ ucfirst($event->status) }}
                    </span>
                    <span class="text-xs" style="color:#6b7280">{{ $event->category->name ?? '—' }}</span>
                </div>

                <h3 class="font-semibold text-white text-lg mb-2 group-hover:text-indigo-300 transition truncate">{{ $event->title }}</h3>

                <div class="flex items-center gap-1 text-sm mb-1" style="color:#9ca3af">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $event->start_date->format('M d') }}
                    @if($event->start_date != $event->end_date) — {{ $event->end_date->format('M d, Y') }}
                    @else, {{ $event->start_date->format('Y') }} @endif
                </div>

                <div class="flex items-center gap-1 text-sm mb-4" style="color:#9ca3af">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $event->event_guests_count ?? 0 }} guests
                    @if($event->venue)
                    <span class="mx-1">·</span>
                    <span class="truncate">{{ $event->venue }}</span>
                    @endif
                </div>

                <a href="{{ route('events.show', $event) }}"
                   class="block text-center py-2.5 text-sm font-medium rounded-xl transition"
                   style="background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.25)"
                   onmouseover="this.style.background='rgba(99,102,241,.25)'"
                   onmouseout="this.style.background='rgba(99,102,241,.15)'">
                    Manage Event
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($events->hasPages())
    <div class="flex justify-center">
        {{ $events->links() }}
    </div>
    @endif

    @else
    <div class="rounded-2xl border py-20 text-center" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:rgba(79,70,229,.1)">
            <svg class="w-7 h-7" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-white font-medium mb-1">No events found</p>
        <p class="text-sm mb-5" style="color:#6b7280">Create your first event to get started.</p>
        <a href="{{ route('events.create') }}"
           class="inline-block px-6 py-2.5 text-white text-sm font-medium rounded-xl"
           style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            Create Event
        </a>
    </div>
    @endif
</div>
</x-app-layout>
