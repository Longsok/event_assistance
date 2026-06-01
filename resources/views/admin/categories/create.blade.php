<x-admin-layout title="New Category">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 text-sm hover:text-indigo-300">Back to Categories</a>
    </div>
    <div class="max-w-lg rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
        <h2 class="text-lg font-semibold mb-5" style="color:var(--text-strong)">New Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>
            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Color</label>
                <input type="color" name="color" value="{{ old('color', '#4f46e5') }}"
                       class="w-12 h-10 rounded cursor-pointer"
                       style="background:var(--input-bg);border:1px solid var(--input-border)">
            </div>
            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Description</label>
                <textarea name="description" rows="2"
                          class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                          style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">{{ old('description') }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="rounded text-indigo-600" style="accent-color:#4f46e5">
                <label for="is_active" class="text-sm" style="color:var(--text-soft)">Active</label>
            </div>
            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">Create Category</button>
        </form>
    </div>
</x-admin-layout>