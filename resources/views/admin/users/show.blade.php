<x-admin-layout>
    <x-slot name="title">User: {{ $user->name }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Back to Users</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center text-xl font-bold text-white">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white font-semibold text-lg">{{ $user->name }}</p>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Role</span>
                    <span class="text-white">{{ $user->roles->pluck('name')->join(', ') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Joined</span>
                    <span class="text-white">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Events</span>
                    <span class="text-white">{{ $user->events->count() }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Events</h3>
            @forelse($user->events as $event)
                <div class="flex items-center justify-between py-2 border-b border-gray-800">
                    <span class="text-gray-300">{{ $event->title }}</span>
                    <span class="text-xs px-2 py-1 rounded bg-gray-800 text-gray-400">{{ $event->status }}</span>
                </div>
            @empty
                <p class="text-gray-500">No events yet.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>
