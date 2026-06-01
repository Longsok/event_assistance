<x-admin-layout title="User Detail">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm hover:underline" style="color:#818cf8">
            &larr; Back to Users
        </a>
    </div>

    {{-- Profile card --}}
    <div class="rounded-xl border p-6 mb-6" style="background:var(--panel);border-color:var(--border)">

        {{-- Header row --}}
        <div class="flex items-start justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center text-xl font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold" style="color:var(--text-strong)">{{ $user->name }}</h2>
                    <p class="text-sm" style="color:var(--text-soft)">{{ $user->email }}</p>
                    <p class="text-xs mt-0.5" style="color:var(--text-soft)">Joined {{ $user->created_at->format('M d, Y') }}</p>
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
        <div class="pt-5" style="border-top:1px solid var(--border)">
            <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:var(--text-soft)">Manage Role</p>
            <div class="flex flex-wrap gap-2 items-center">

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
                <div class="w-px mx-1 self-stretch" style="background:var(--border)"></div>

                {{-- Delete --}}
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                      onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? All their data will be removed.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition"
                            style="background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.25)"
                            onmouseover="this.style.background='rgba(239,68,68,.2)'"
                            onmouseout="this.style.background='rgba(239,68,68,.1)'">
                        Delete User
                    </button>
                </form>

            </div>

            {{-- Status hint --}}
            <p class="text-xs mt-3" style="color:var(--text-soft)">
                @if($user->hasRole('admin'))
                    Currently <span style="color:#a78bfa">Admin</span> — can manage the entire system.
                @elseif($user->hasRole('organizer'))
                    Currently <span style="color:#818cf8">Organizer</span> — can create and manage events.
                @else
                    Currently <span style="color:var(--text-strong)">User</span> — no special permissions.
                @endif
            </p>
        </div>
        @else
        <div class="pt-4" style="border-top:1px solid var(--border)">
            <p class="text-xs" style="color:var(--text-soft)">This is your own account — role changes and deletion are disabled.</p>
        </div>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border p-4 text-center" style="background:var(--panel);border-color:var(--border)">
            <p class="text-2xl font-bold" style="color:var(--text-strong)">{{ $user->events->count() }}</p>
            <p class="text-xs mt-1" style="color:var(--text-soft)">Events</p>
        </div>
        <div class="rounded-xl border p-4 text-center" style="background:var(--panel);border-color:var(--border)">
            <p class="text-2xl font-bold" style="color:var(--text-strong)">
                {{ $user->events->sum(fn($e) => $e->eventGuests->count()) }}
            </p>
            <p class="text-xs mt-1" style="color:var(--text-soft)">Total Guests</p>
        </div>
        <div class="rounded-xl border p-4 text-center" style="background:var(--panel);border-color:var(--border)">
            <p class="text-2xl font-bold" style="color:var(--text-strong)">
                {{ $user->events->where('status', 'completed')->count() }}
            </p>
            <p class="text-xs mt-1" style="color:var(--text-soft)">Completed Events</p>
        </div>
    </div>

    {{-- Events list --}}
    <div class="rounded-xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <div class="px-6 py-4" style="border-bottom:1px solid var(--border)">
            <h3 class="font-semibold" style="color:var(--text-strong)">Events ({{ $user->events->count() }})</h3>
        </div>
        @forelse($user->events as $event)
        <div class="flex items-center justify-between px-6 py-4 transition" style="border-bottom:1px solid var(--border)"
             onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
            <div class="min-w-0">
                <p class="text-sm font-medium truncate" style="color:var(--text-strong)">{{ $event->title }}</p>
                <p class="text-xs mt-0.5" style="color:var(--text-soft)">
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
        <div class="px-6 py-10 text-center text-sm" style="color:var(--text-soft)">No events yet.</div>
        @endforelse
    </div>
</x-admin-layout>