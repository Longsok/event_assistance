<x-admin-layout title="New Category">
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 text-sm hover:text-indigo-300">Back to Categories</a>
    </div>
    <div class="max-w-lg rounded-xl border border-gray-800 p-6" style="background:#111827">
        <h2 class="text-lg font-semibold text-white mb-5">New Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-indigo-500">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Color</label>
                <input type="color" name="color" value="{{ old('color', '#4f46e5') }}"
                       class="w-12 h-10 rounded bg-gray-800 border border-gray-700 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Description</label>
                <textarea name="description" rows="2"
                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="rounded border-gray-600 bg-gray-800 text-indigo-600">
                <label for="is_active" class="text-sm text-gray-400">Active</label>
            </div>
            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">Create Category</button>
        </form>
    </div>
</x-admin-layout>
