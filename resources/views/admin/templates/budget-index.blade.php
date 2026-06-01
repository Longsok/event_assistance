<x-admin-layout title="Budget Templates">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold" style="color:var(--text-strong)">Budget Templates</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $category)
        <a href="{{ route('admin.budget-templates.show', $category) }}"
           class="rounded-xl p-5 border transition group" style="background:var(--panel);border-color:var(--border)"
           onmouseover="this.style.borderColor='#6366f1'" onmouseout="this.style.borderColor='var(--border)'">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-4 h-4 rounded-full" style="background:{{ $category->color }}"></div>
                <p class="font-semibold group-hover:text-indigo-400 transition" style="color:var(--text-strong)">{{ $category->name }}</p>
            </div>
            <p class="text-sm" style="color:var(--text-soft)">{{ $category->budget_templates_count ?? 0 }} budget items</p>
            <p class="text-indigo-400 text-xs mt-2">Manage templates</p>
        </a>
        @empty
        <p class="text-sm col-span-3 text-center py-10" style="color:var(--text-soft)">No categories yet.</p>
        @endforelse
    </div>
</x-admin-layout>