<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ $event->title }}
            </a>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">Guest List</h2>
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([['label'=>'Total','value'=>$stats['total'],'color'=>'text-slate-900'],['label'=>'Confirmed','value'=>$stats['confirmed'],'color'=>'text-emerald-600'],['label'=>'Pending','value'=>$stats['pending'],'color'=>'text-amber-600'],['label'=>'Declined','value'=>$stats['declined'],'color'=>'text-red-600']] as $s)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>
    @if($availableGuests->count())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-3">Add Guest to Event</h3>
        <form method="POST" action="{{ route('events.guests.store', $event) }}" class="flex gap-3">
            @csrf
            <select name="guest_id" required class="flex-1 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                <option value="">Select from your guest book...</option>
                @foreach($availableGuests as $g)
                <option value="{{ $g->id }}">{{ $g->name }}{{ $g->email ? ' ('.$g->email.')' : '' }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Add</button>
        </form>
    </div>
    @endif
    <livewire:guest-search :event="$event" />
</div>
</x-app-layout>
