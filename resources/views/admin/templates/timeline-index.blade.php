<x-admin-layout title="Category Templates">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold" style="color:var(--text-strong)">Task Templates</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $category)
        <a href="{{ route('admin.category-templates.show', $category) }}"
           class="rounded-xl p-5 border transition group" style="background:var(--panel);border-color:var(--border)"
           onmouseover="this.style.borderColor='#6366f1'" onmouseout="this.style.borderColor='var(--border)'">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-4 h-4 rounded-full" style="background:{{ $category->color }}"></div>
                <p class="font-semibold group-hover:text-indigo-400 transition" style="color:var(--text-strong)">{{ $category->name }}</p>
            </div>
            <p class="text-sm" style="color:var(--text-soft)">{{ $category->category_templates_count ?? 0 }} task templates</p>
            <p class="text-indigo-400 text-xs mt-2">Manage templates</p>
        </a>
        @empty
        <div class="col-span-3 text-center py-10">
            <p class="mb-3" style="color:var(--text-soft)">No categories yet.</p>
            <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">Create Category</a>
        </div>
        @endforelse
    </div>
</x-admin-layout>