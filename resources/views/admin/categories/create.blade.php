<x-admin-layout>
    <x-slot name="title">New Category</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm">&larr; Back</a>
    </div>

    <div class="max-w-lg bg-gray-900 rounded-xl border border-gray-800 p-6">
        <h2 class="text-white font-semibold text-lg mb-6">Create Category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
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
            <div>
                <label class="block text-sm text-gray-400 mb-1">Icon (optional)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="e.g. 🎉"
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">{{ old('description') }}</textarea>
            </div>
            <button type="submit"
                    class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                Create Category
            </button>
        </form>
    </div>
</x-admin-layout>
