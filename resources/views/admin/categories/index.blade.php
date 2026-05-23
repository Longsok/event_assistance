<x-admin-layout title="Categories">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">Event Categories</h2>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition">+ New Category</a>
    </div>
    <div class="rounded-xl border border-gray-800 overflow-hidden" style="background:#111827">
        <table class="w-full text-sm">
            <thead class="text-gray-400 uppercase text-xs border-b border-gray-800" style="background:#1f2937">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Color</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full" style="background:{{ $cat->color }}"></div>
                            <span class="text-white font-medium">{{ $cat->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $cat->color }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $cat->events_count ?? 0 }}</td>
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
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
