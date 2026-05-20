<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
                <h2 class="text-2xl font-bold text-gray-900 mt-1">Guest List</h2>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label'=>'Total',     'value'=>$stats['total'],     'color'=>'text-gray-900'],
                ['label'=>'Confirmed', 'value'=>$stats['confirmed'], 'color'=>'text-green-600'],
                ['label'=>'Pending',   'value'=>$stats['pending'],   'color'=>'text-yellow-600'],
                ['label'=>'Declined',  'value'=>$stats['declined'],  'color'=>'text-red-600'],
            ] as $s)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 text-center">
                <p class="text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $s['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Add Guest --}}
        @if($availableGuests->count())
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Add Guest to Event</h3>
            <form method="POST" action="{{ route('events.guests.store', $event) }}" class="flex gap-3">
                @csrf
                <select name="guest_id"
                        class="flex-1 border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                        required>
                    <option value="">Select from your guest book...</option>
                    @foreach($availableGuests as $g)
                    <option value="{{ $g->id }}">{{ $g->name }}{{ $g->email ? ' ('.$g->email.')' : '' }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                    Add
                </button>
            </form>
        </div>
        @else
        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-sm text-indigo-700">
            All guests from your guest book are already added to this event.
            <a href="{{ route('guests.create') }}" class="font-medium underline ml-1">Add new guest →</a>
        </div>
        @endif

        {{-- Live Guest Search (Livewire) --}}
        <livewire:guest-search :event="$event" />

    </div>
</x-app-layout>
