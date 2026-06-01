<x-admin-layout>
    <x-slot name="title">Budget Templates</x-slot>

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.budget-templates.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Categories</a>
        <span style="color:var(--text-soft)">/</span>
        <span class="text-sm" style="color:var(--text-strong)">Budget Templates: {{ $category->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Add Budget Item</h3>
            <form method="POST" action="{{ route('admin.budget-templates.store', $category) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Line Item *</label>
                    <input type="text" name="line_item" value="{{ old('line_item') }}"
                           class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)" required>
                </div>
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Suggested % *</label>
                    <input type="number" name="suggested_percentage" value="{{ old('suggested_percentage') }}" min="0" max="100" step="0.1"
                           class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)" required>
                </div>
                <div>
                    <label class="block text-sm mb-1" style="color:var(--text-soft)">Scale Trigger</label>
                    <input type="text" name="scale_trigger" value="{{ old('scale_trigger', 'any') }}"
                           class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Add Item
                </button>
            </form>
        </div>

        <div class="rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Items ({{ $templates->count() }})</h3>
            <div class="space-y-2">
                @forelse ($templates as $template)
                <div class="flex items-center justify-between p-3 rounded-lg text-sm" style="background:var(--panel-input)">
                    <div>
                        <p style="color:var(--text-strong)">{{ $template->line_item }}</p>
                        <p class="text-xs" style="color:var(--text-soft)">{{ $template->suggested_percentage }}%</p>
                    </div>
                    <form method="POST" action="{{ route('admin.budget-templates.destroy', [$category, $template]) }}"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </div>
                @empty
                <p class="text-sm" style="color:var(--text-soft)">No budget items yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>