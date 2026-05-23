<x-admin-layout title="User Detail">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-slate-400 text-sm hover:text-white">
            &larr; Back to Users
        </a>
    </div>

    {{-- Profile card --}}
    <div class="rounded-xl border border-gray-800 p-6 mb-6" style="background:#111827">

        {{-- Header row --}}
        <div class="flex items-start justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center text-xl font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    <p class="text-gray-600 text-xs mt-0.5">Joined {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            {{-- Current role badges --}}
            <div class="flex flex-wrap gap-2 pt-1">
                @forelse($user->roles as $role)
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $role->name === 'admin'
                        ? 'bg-violet-900/50 text-violet-300 border border-violet-700/50'
                        : ($role->name === 'organizer'
                            ? 'bg-indigo-900/50 text-indigo-300 border border-indigo-700/50'
                            : 'bg-gray-800 text-gray-300 border border-gray-700') }}">
                    {{ ucfirst($role->name) }}
                </span>
                @empty
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-800 text-gray-500 border border-gray-700">
                    No Role
                </span>
                @endforelse
            </div>
        </div>

        {{-- Role buttons + delete --}}
        @if($user->id !== auth()->id())
        <div class="border-t border-gray-800 pt-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Manage Role</p>
            <div class="flex flex-wrap gap-2">

                {{-- Set as User --}}
                <form method="POST" action="{{ route('admin.users.setRole', $user) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="role" value="user">
                    <button type="submit"
                            onclick="return confirm('Set {{ addslashes($user->name) }} as plain User? They will lose organizer/admin access.')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition
                                   {{ !$user->hasRole('organizer') && !$user->hasRole('admin')
                                        ? 'bg-gray-700 text-gray-200 border border-gray-500 opacity-60 cursor-default'
                                        : 'bg-gray-800 hover:bg-gray-700 text-gray-300 border border-gray-700 hover:border-gray-500' }}"
                            {{ !$user->hasRole('organizer') && !$user->hasRole('admin') ? 'disabled' : '' }}>
                        Set as User
                    </button>
                </form>

                {{-- Set as Organizer --}}
                <form method="POST" action="{{ route('admin.users.setRole', $user) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="role" value="organizer">
                    <button type="submit"
                            onclick="return confirm('Set {{ addslashes($user->name) }} as Organizer?')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition
                                   {{ $user->hasRole('organizer') && !$user->hasRole('admin')
                                        ? 'bg-indigo-800/60 text-indigo-200 border border-indigo-600 opacity-60 cursor-default'
                                        : 'bg-indigo-900/30 hover:bg-indigo-900/60 text-indigo-400 border border-indigo-800 hover:border-indigo-600' }}"
                            {{ $user->hasRole('organizer') && !$user->hasRole('admin') ? 'disabled' : '' }}>
                        Set as Organizer
                    </button>
                </form>

                {{-- Set as Admin --}}
                <form method="POST" action="{{ route('admin.users.setRole', $user) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="role" value="admin">
                    <button type="submit"
                            onclick="return confirm('Promote {{ addslashes($user->name) }} to Admin? They will have full admin access.')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition
                                   {{ $user->hasRole('admin')
                                        ? 'bg-violet-800/60 text-violet-200 border border-violet-600 opacity-60 cursor-default'
                                        : 'bg-violet-900/30 hover:bg-violet-900/60 text-violet-400 border border-violet-800 hover:border-violet-600' }}"
                            {{ $user->hasRole('admin') ? 'disabled' : '' }}>
                        Set as Admin
                    </button>
                </form>

                {{-- Divider --}}
                <div class="w-px bg-gray-800 mx-1"></div>

                {{-- Delete --}}
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? All their data will be removed.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition
                                   bg-gray-900 hover:bg-gray-800 text-gray-500 hover:text-gray-300
                                   border border-gray-800 hover:border-gray-600">
                        Delete User
                    </button>
                </form>

            </div>

            {{-- Status hint --}}
            <p class="text-xs text-gray-600 mt-3">
                @if($user->hasRole('admin'))
                    Currently <span class="text-violet-500">Admin</span> — can manage the entire system.
                @elseif($user->hasRole('organizer'))
                    Currently <span class="text-indigo-500">Organizer</span> — can create and manage events.
                @else
                    Currently <span class="text-gray-400">User</span> — no special permissions.
                @endif
            </p>
        </div>
        @else
        <div class="border-t border-gray-800 pt-4">
            <p class="text-xs text-gray-600">This is your own account — role changes and deletion are disabled.</p>
        </div>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-gray-800 p-4 text-center" style="background:#111827">
            <p class="text-2xl font-bold text-white">{{ $user->events->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Events</p>
        </div>
        <div class="rounded-xl border border-gray-800 p-4 text-center" style="background:#111827">
            <p class="text-2xl font-bold text-white">
                {{ $user->events->sum(fn($e) => $e->eventGuests->count()) }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Total Guests</p>
        </div>
        <div class="rounded-xl border border-gray-800 p-4 text-center" style="background:#111827">
            <p class="text-2xl font-bold text-white">
                {{ $user->events->where('status', 'completed')->count() }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Completed Events</p>
        </div>
    </div>

    {{-- Events list --}}
    <div class="rounded-xl border border-gray-800 overflow-hidden" style="background:#111827">
        <div class="px-6 py-4 border-b border-gray-800">
            <h3 class="text-white font-semibold">Events ({{ $user->events->count() }})</h3>
        </div>
        @forelse($user->events as $event)
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800 last:border-0 hover:bg-gray-800/50 transition">
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ $event->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $event->category->name ?? '-' }} &middot;
                    {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : '-' }}
                </p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $event->status === 'ongoing'    ? 'bg-emerald-900/40 text-emerald-400' :
                       ($event->status === 'completed' ? 'bg-blue-900/40 text-blue-400' :
                       ($event->status === 'draft'     ? 'bg-gray-800 text-gray-400' :
                        'bg-amber-900/40 text-amber-400')) }}">
                    {{ ucfirst($event->status) }}
                </span>
                <a href="{{ route('admin.events.show', $event) }}"
                   class="text-xs text-indigo-400 hover:text-indigo-300">View</a>
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-gray-500 text-sm">No events yet.</div>
        @endforelse
    </div>
</x-admin-layout>
