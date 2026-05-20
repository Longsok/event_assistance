<x-admin-layout>
    <x-slot name="title">Users</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">All Users</h2>
        <span class="text-sm text-gray-400">{{ $users->total() }} total</span>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-left">Joined</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="text-white font-medium">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @foreach($user->roles as $role)
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $role->name === 'admin' ? 'bg-red-900/40 text-red-400' : 'bg-indigo-900/40 text-indigo-400' }}">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->events_count }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($user->id !== auth()->id())
                                @if($user->hasRole('admin'))
                                    <form method="POST" action="{{ route('admin.users.demote', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2 py-1 rounded bg-yellow-900/40 text-yellow-400 hover:bg-yellow-900/70">Demote</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2 py-1 rounded bg-green-900/40 text-green-400 hover:bg-green-900/70">Promote</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs px-2 py-1 rounded bg-red-900/40 text-red-400 hover:bg-red-900/70">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
