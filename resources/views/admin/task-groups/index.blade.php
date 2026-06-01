<x-admin-layout title="Task Groups">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Add Task Group</h3>
            <form method="POST" action="{{ route('admin.task-groups.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Color</label>
                    <input type="color" name="color" value="{{ old('color', '#4f46e5') }}"
                           class="w-12 h-10 rounded cursor-pointer"
                           style="background:var(--input-bg);border:1px solid var(--input-border)">
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">Add Group</button>
            </form>
        </div>
        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Existing Groups ({{ $groups->count() }})</h3>
            <div class="space-y-2">
                @forelse($groups as $group)
                <div class="flex items-center justify-between p-3 rounded-lg" style="background:var(--panel-input)">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full" style="background:{{ $group->color }}"></div>
                        <span class="text-sm" style="color:var(--text-strong)">{{ $group->name }}</span>
                        <span class="text-xs" style="color:var(--text-soft)">{{ $group->event_tasks_count ?? 0 }} tasks</span>
                    </div>
                    <form method="POST" action="{{ route('admin.task-groups.destroy', $group) }}" onsubmit="return confirm('Delete group?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300 font-medium">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-sm" style="color:var(--text-soft)">No groups yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>