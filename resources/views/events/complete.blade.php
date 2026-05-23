<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-xl font-semibold text-slate-900 mt-1">Complete Event</h2>
    </div>
    @if($completion)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-slate-800 text-lg">Event Summary</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            @foreach([
                ['label'=>'Attendance','value'=>$completion->total_attended.'/'.$completion->total_invited,'sub'=>$completion->attendance_rate.'% rate'],
                ['label'=>'Tasks','value'=>$completion->tasks_completed.'/'.$completion->tasks_total,'sub'=>$completion->task_completion_rate.'% complete'],
                ['label'=>'Budget Spent','value'=>'$'.number_format($completion->total_spent,2),'sub'=>'of $'.number_format($completion->total_budget,2)],
                ['label'=>'Contributions','value'=>'$'.number_format($completion->total_contributions,2),'sub'=>'received'],
            ] as $s)
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-slate-500 text-xs mb-1">{{ $s['label'] }}</p>
                <p class="font-bold text-slate-900 text-xl">{{ $s['value'] }}</p>
                <p class="text-xs text-slate-400">{{ $s['sub'] }}</p>
            </div>
            @endforeach
        </div>
        @if($completion->organizer_notes)
        <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
            <p class="text-sm text-slate-700">{{ $completion->organizer_notes }}</p>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <p class="text-slate-600 text-sm mb-6">Mark this event as completed. A final summary report will be generated.</p>
        <form method="POST" action="{{ route('events.completion.store', $event) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Organizer Notes (optional)</label>
                <textarea name="organizer_notes" rows="4" placeholder="How did the event go? Any notes for next time..."
                          class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">{{ old('organizer_notes') }}</textarea>
            </div>
            <button type="submit" onclick="return confirm('Mark this event as completed?')"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-medium transition">
                Mark as Completed
            </button>
        </form>
    </div>
    @endif
</div>
</x-app-layout>
