<x-app-layout>
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Guest Book</h2>
            <p class="text-slate-500 text-sm mt-1">{{ $guests->total() }} guests total</p>
        </div>
        <a href="{{ route('guests.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Guest
        </a>
    </div>
    <form method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <div class="flex gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                       class="w-full pl-9 pr-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Search</button>
            @if(request('search'))
            <a href="{{ route('guests.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-xl transition">Clear</a>
            @endif
        </div>
    </form>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-left">Guest</th>
                    <th class="px-6 py-3 text-left">Phone</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($guests as $guest)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($guest->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">{{ $guest->name }}</p>
                                <p class="text-xs text-slate-400">{{ $guest->email ?? 'No email' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $guest->phone ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">{{ $guest->event_guests_count ?? 0 }} events</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('guests.edit', $guest) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                            <form method="POST" action="{{ route('guests.destroy', $guest) }}" onsubmit="return confirm('Delete {{ $guest->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <p class="text-slate-500">{{ request('search') ? 'No guests match your search.' : 'No guests yet. Add your first guest!' }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $guests->links() }}</div>
</div>
</x-app-layout>
