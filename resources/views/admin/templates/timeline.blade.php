<x-admin-layout>
    <x-slot name="title">Task Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.category-templates.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span style="color:var(--text-soft)">/</span>
        <span class="text-sm" style="color:var(--text-strong)">Task Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Add Task Template</h3>
            <form method="POST" action="{{ route('admin.category-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Task Name *</label>
                    <input type="text" name="task_name" value="{{ old('task_name') }}"
                           class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)" required>
                </div>
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Group *</label>
                    <select name="group_id" class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                            style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm mb-1" style="color:var(--text-soft)">Days Before *</label>
                        <input type="number" name="days_before" value="{{ old('days_before', 7) }}"
                               class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                               style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-1" style="color:var(--text-soft)">Priority *</label>
                        <select name="priority" class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                                style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Anchor *</label>
                    <select name="anchor" class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                            style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
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

        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Tasks ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 rounded-lg text-sm" style="background:var(--panel-input)">
                    <div>
                        <p style="color:var(--text-strong)">{{ $template->task_name }}</p>
                        <p class="text-xs" style="color:var(--text-soft)">{{ $template->group->name ?? '-' }} · {{ $template->days_before }}d · {{ $template->priority }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.category-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-sm" style="color:var(--text-soft)">No task templates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>