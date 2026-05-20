<x-admin-layout>
    <x-slot name="title">Task Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.category-templates.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span class="text-gray-600">/</span>
        <span class="text-white text-sm">Task Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Task Template</h3>
            <form method="POST" action="{{ route('admin.category-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Task Name *</label>
                    <input type="text" name="task_name" value="{{ old('task_name') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Group *</label>
                    <select name="group_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Days Before *</label>
                        <input type="number" name="days_before" value="{{ old('days_before', 7) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Priority *</label>
                        <select name="priority" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Anchor *</label>
                    <select name="anchor" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                        <option value="before_event">Before Event</option>
                        <option value="first_day">First Day</option>
                        <option value="last_day">Last Day</option>
                        <option value="after_event">After Event</option>
                        <option value="proportional">Proportional</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Task
                </button>
            </form>
        </div>

        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Tasks ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg text-sm">
                    <div>
                        <p class="text-white">{{ $template->task_name }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->group->name ?? '-' }} · {{ $template->days_before }}d · {{ $template->priority }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.category-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No task templates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
