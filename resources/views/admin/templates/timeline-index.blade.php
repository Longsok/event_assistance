<x-admin-layout>
    <x-slot name="title">Category Templates</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">Task Templates — Select a Category</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $category)
        <a href="{{ route('admin.category-templates.show', $category) }}"
           class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-indigo-500 transition group">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-4 h-4 rounded-full flex-shrink-0" style="background: {{ $category->color }}"></div>
                <p class="text-white font-semibold group-hover:text-indigo-400 transition">{{ $category->name }}</p>
            </div>
            <p class="text-gray-400 text-sm">{{ $category->category_templates_count }} task templates</p>
            <p class="text-indigo-400 text-xs mt-2">Manage templates &rarr;</p>
        </a>
        @empty
        <div class="col-span-3 text-center py-10">
            <p class="text-gray-500 mb-3">No categories yet.</p>
            <a href="{{ route('admin.categories.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                Create Category
            </a>
        </div>
        @endforelse
    </div>
</x-admin-layout>
