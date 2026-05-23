<x-admin-layout title="Category Templates">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">Task Templates</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $category)
        <a href="{{ route('admin.category-templates.show', $category) }}"
           class="rounded-xl p-5 border border-gray-800 hover:border-indigo-500 transition group" style="background:#111827">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-4 h-4 rounded-full" style="background:{{ $category->color }}"></div>
                <p class="text-white font-semibold group-hover:text-indigo-400 transition">{{ $category->name }}</p>
            </div>
            <p class="text-gray-400 text-sm">{{ $category->category_templates_count ?? 0 }} task templates</p>
            <p class="text-indigo-400 text-xs mt-2">Manage templates</p>
        </a>
        @empty
        <div class="col-span-3 text-center py-10">
            <p class="text-gray-500 mb-3">No categories yet.</p>
            <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">Create Category</a>
        </div>
        @endforelse
    </div>
</x-admin-layout>
