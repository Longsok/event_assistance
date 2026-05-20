<x-admin-layout>
    <x-slot name="title">Schedule Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.schedule-templates.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span class="text-gray-600">/</span>
        <span class="text-white text-sm">Schedule Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Session</h3>
            <form method="POST" action="{{ route('admin.schedule-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Session Name *</label>
                    <input type="text" name="session_name" value="{{ old('session_name') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Anchor *</label>
                        <select name="anchor" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                            <option value="start">Start</option>
                            <option value="end">End</option>
                            <option value="middle">Middle</option>
                            <option value="proportional">Proportional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Offset (mins) *</label>
                        <input type="number" name="offset_minutes" value="{{ old('offset_minutes', 0) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Duration (mins) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_break" id="is_break" value="1">
                    <label for="is_break" class="text-sm text-gray-400">Is Break</label>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Session
                </button>
            </form>
        </div>

        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Sessions ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg text-sm">
                    <div>
                        <p class="text-white">{{ $template->session_name }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->duration_minutes }} mins · {{ $template->anchor }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.schedule-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No sessions yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
