<x-admin-layout title="Users">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold" style="color:var(--text-strong)">All Users</h2>
            <p class="text-sm mt-0.5" style="color:var(--text-soft)">{{ $users->total() }} total</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New User
        </a>
    </div>

    <div class="rounded-xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <table class="w-full text-sm">
            <thead class="uppercase text-xs" style="background:var(--panel-input);color:var(--text-soft);border-bottom:1px solid var(--border)">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-left">Joined</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="border-bottom:1px solid var(--border)"
                    onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-medium" style="color:var(--text-strong)">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $role->name === 'admin' ? 'bg-red-900/40 text-red-400' : 'bg-indigo-900/40 text-indigo-400' }}">
                            {{ $role->name }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $user->events_count ?? 0 }}</td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">View</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-400 hover:text-red-300 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center" style="color:var(--text-soft)">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>