<x-admin-layout>
    <x-slot name="title">Task Groups</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Add Form --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Task Group</h3>
            <form method="POST" action="{{ route('admin.task-groups.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500"
                           required>
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Color</label>
                    <input type="color" name="color" value="{{ old('color', '#534AB7') }}"
                           class="w-12 h-10 rounded bg-gray-800 border border-gray-700 cursor-pointer">
                </div>
                <button type="submit"
                        class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Group
                </button>
            </form>
        </div>

        {{-- Groups List --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Existing Groups ({{ $groups->count() }})</h3>
            <div class="space-y-2">
                @forelse ($groups as $group)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full" style="background:{{ $group->color }}"></div>
                        <span class="text-white text-sm">{{ $group->name }}</span>
                        <span class="text-xs text-gray-500">{{ $group->event_tasks_count }} tasks</span>
                    </div>
                    <form method="POST" action="{{ route('admin.task-groups.destroy', $group) }}"
                          onsubmit="return confirm('Delete this group?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No task groups yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
