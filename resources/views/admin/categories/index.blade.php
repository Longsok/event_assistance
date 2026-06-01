<x-admin-layout title="Categories">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold" style="color:var(--text-strong)">Event Categories</h2>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition">+ New Category</a>
    </div>
    <div class="rounded-xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <table class="w-full text-sm">
            <thead class="uppercase text-xs" style="background:var(--panel-input);color:var(--text-soft);border-bottom:1px solid var(--border)">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Color</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr style="border-bottom:1px solid var(--border)"
                    onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full" style="background:{{ $cat->color }}"></div>
                            <span class="font-medium" style="color:var(--text-strong)">{{ $cat->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $cat->color }}</td>
                    <td class="px-6 py-4" style="color:var(--text-soft)">{{ $cat->events_count ?? 0 }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs {{ $cat->is_active ? 'bg-emerald-900/40 text-emerald-400' : 'bg-gray-800 text-gray-500' }}">
                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-medium">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-400 hover:text-red-300 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center" style="color:var(--text-soft)">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>