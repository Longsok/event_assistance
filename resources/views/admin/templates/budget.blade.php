<x-admin-layout>
    <x-slot name="title">Budget Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.budget-templates.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span class="text-gray-600">/</span>
        <span class="text-white text-sm">Budget Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Add Budget Item</h3>
            <form method="POST" action="{{ route('admin.budget-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Line Item *</label>
                    <input type="text" name="line_item" value="{{ old('line_item') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Suggested % *</label>
                    <input type="number" name="suggested_percentage" value="{{ old('suggested_percentage') }}" min="0" max="100" step="0.1"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Scale Trigger</label>
                    <input type="text" name="scale_trigger" value="{{ old('scale_trigger', 'any') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Item
                </button>
            </form>
        </div>

        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-white font-semibold mb-4">Items ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg text-sm">
                    <div>
                        <p class="text-white">{{ $template->line_item }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->suggested_percentage }}%</p>
                    </div>
                    <form method="POST" action="{{ route('admin.budget-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No budget items yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
