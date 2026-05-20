<x-admin-layout>
    <x-slot name="title">Categories</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">Event Categories</h2>
        <a href="{{ route('admin.categories.create') }}"
           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
            + New Category
        </a>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Color</th>
                    <th class="px-6 py-3 text-left">Events</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($categories as $category)
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full" style="background:{{ $category->color }}"></div>
                            <span class="text-white font-medium">{{ $category->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $category->color }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $category->events_count }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs {{ $category->is_active ? 'bg-green-900/40 text-green-400' : 'bg-gray-800 text-gray-500' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="text-xs px-2 py-1 rounded bg-indigo-900/40 text-indigo-400 hover:bg-indigo-900/70">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="text-xs px-2 py-1 rounded bg-red-900/40 text-red-400 hover:bg-red-900/70">Delete</button>
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
