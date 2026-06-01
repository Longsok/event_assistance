<x-admin-layout title="Event Detail">
    <div class="mb-6">
        <a href="{{ url()->previous(route('admin.events.index')) }}" class="text-indigo-400 text-sm hover:text-indigo-300">Back</a>
    </div>
    <div class="rounded-xl border p-6 space-y-4" style="background:var(--panel);border-color:var(--border)">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold" style="color:var(--text-strong)">{{ $event->title }}</h2>
                <p class="text-sm mt-1" style="color:var(--text-soft)">{{ $event->user->name ?? '-' }} &middot; {{ $event->category->name ?? '-' }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-full text-sm {{ $event->status==='ongoing' ? 'bg-emerald-900/40 text-emerald-400' : 'bg-gray-800 text-gray-400' }}">
                {{ ucfirst($event->status) }}
            </span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 pt-4" style="border-top:1px solid var(--border)">
            @foreach([['label'=>'Guests','value'=>$event->eventGuests->count()],['label'=>'Tasks','value'=>$event->tasks->count()],['label'=>'Budget','value'=>'$'.number_format($event->budget?->total_budget ?? 0,0)],['label'=>'Sessions','value'=>$event->schedules->count()]] as $s)
            <div class="text-center p-4 rounded-xl" style="background:var(--panel-input)">
                <p class="text-2xl font-bold" style="color:var(--text-strong)">{{ $s['value'] }}</p>
                <p class="text-xs mt-1" style="color:var(--text-soft)">{{ $s['label'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>