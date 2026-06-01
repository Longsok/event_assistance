<x-admin-layout>
    <x-slot name="title">Schedule Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.schedule-templates.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span style="color:var(--text-soft)">/</span>
        <span class="text-sm" style="color:var(--text-strong)">Schedule Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Add Session</h3>
            <form method="POST" action="{{ route('admin.schedule-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Session Name *</label>
                    <input type="text" name="session_name" value="{{ old('session_name') }}"
                           class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm mb-1" style="color:var(--text-soft)">Anchor *</label>
                        <select name="anchor" class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                                style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                            <option value="start">Start</option>
                            <option value="end">End</option>
                            <option value="middle">Middle</option>
                            <option value="proportional">Proportional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1" style="color:var(--text-soft)">Offset (mins) *</label>
                        <input type="number" name="offset_minutes" value="{{ old('offset_minutes', 0) }}"
                               class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                               style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Duration (mins) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1"
                           class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_break" id="is_break" value="1" style="accent-color:#4f46e5">
                    <label for="is_break" class="text-sm" style="color:var(--text-soft)">Is Break</label>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Session
                </button>
            </form>
        </div>

        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Sessions ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 rounded-lg text-sm" style="background:var(--panel-input)">
                    <div>
                        <p style="color:var(--text-strong)">{{ $template->session_name }}</p>
                        <p class="text-xs" style="color:var(--text-soft)">{{ $template->duration_minutes }} mins · {{ $template->anchor }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.schedule-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-sm" style="color:var(--text-soft)">No sessions yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>