<x-admin-layout>
    <x-slot name="title">Events</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-white">All Events</h2>
    </div>

    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Organizer</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($events ?? [] as $event)
                <tr class="hover:bg-gray-800/50">
                    <td class="px-6 py-4 text-white font-medium">{{ $event->title }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->user->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->category->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs
                            {{ $event->status === 'ongoing' ? 'bg-green-900/40 text-green-400' : 'bg-gray-800 text-gray-400' }}">
                            {{ $event->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $event->start_date ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
