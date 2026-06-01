<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-sm hover:underline" style="color:#818cf8">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ $event->title }}
            </a>
            <h2 class="text-2xl font-bold mt-1" style="color:var(--text-strong)">Guest List</h2>
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([['label'=>'Total','value'=>$stats['total'],'color'=>'var(--text-strong)'],['label'=>'Confirmed','value'=>$stats['confirmed'],'color'=>'#34d399'],['label'=>'Pending','value'=>$stats['pending'],'color'=>'#fbbf24'],['label'=>'Declined','value'=>$stats['declined'],'color'=>'#f87171']] as $s)
        <div class="rounded-2xl border shadow-sm p-4 text-center" style="background:var(--panel);border-color:var(--border)">
            <p class="text-2xl font-bold" style="color:{{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs mt-1" style="color:var(--text-soft)">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>
    @if($availableGuests->count())
    <div class="rounded-2xl border shadow-sm p-5" style="background:var(--panel);border-color:var(--border)">
        <h3 class="font-semibold mb-3" style="color:var(--text-strong)">Add Guest to Event</h3>
        <form method="POST" action="{{ route('events.guests.store', $event) }}" class="flex gap-3">
            @csrf
            <select name="guest_id" required class="flex-1 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                <option value="" style="background:var(--panel)">Select from your guest book...</option>
                @foreach($availableGuests as $g)
                <option value="{{ $g->id }}" style="background:var(--panel)">{{ $g->name }}{{ $g->email ? ' ('.$g->email.')' : '' }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Add</button>
        </form>
    </div>
    @endif
    <livewire:guest-search :event="$event" />
</div>
</x-app-layout>