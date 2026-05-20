<div class="space-y-4">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search guests by name or email..."
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <select wire:model.live="rsvpFilter"
                    class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                <option value="">All RSVP</option>
                <option value="confirmed">Confirmed</option>
                <option value="pending">Pending</option>
                <option value="declined">Declined</option>
                <option value="attended">Attended</option>
            </select>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-700">Guests</p>
            <p class="text-xs text-gray-400">{{ $guests->total() }} results</p>
        </div>
        @forelse($guests as $eg)
        <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr($eg->guest->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 truncate">{{ $eg->guest->name ?? '-' }}</p>
                <p class="text-xs text-gray-400">{{ $eg->guest->email ?? 'No email' }} @if($eg->guest_code) · {{ $eg->guest_code }} @endif</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                {{ $eg->rsvp_status === 'confirmed' ? 'bg-green-100 text-green-700' :
                   ($eg->rsvp_status === 'declined'  ? 'bg-red-100 text-red-700' :
                   ($eg->rsvp_status === 'attended'  ? 'bg-blue-100 text-blue-700' :
                    'bg-gray-100 text-gray-600')) }}">
                {{ ucfirst($eg->rsvp_status) }}
            </span>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-gray-400">
            No guests found{{ $search ? ' for "' . $search . '"' : '' }}.
        </div>
        @endforelse
    </div>
    <div>{{ $guests->links() }}</div>
</div>
