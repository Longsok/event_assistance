<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Complete Event</h2>
        </div>

        @if($completion)
        {{-- Show summary --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h3 class="font-semibold text-gray-800 text-lg">Event Summary</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Attendance</p>
                    <p class="font-bold text-gray-900 text-lg">{{ $completion->total_attended }}/{{ $completion->total_invited }}</p>
                    <p class="text-xs text-gray-400">{{ $completion->attendance_rate }}% rate</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Tasks</p>
                    <p class="font-bold text-gray-900 text-lg">{{ $completion->tasks_completed }}/{{ $completion->tasks_total }}</p>
                    <p class="text-xs text-gray-400">{{ $completion->task_completion_rate }}% complete</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Budget Spent</p>
                    <p class="font-bold text-gray-900 text-lg">${{ number_format($completion->total_spent, 2) }}</p>
                    <p class="text-xs text-gray-400">of ${{ number_format($completion->total_budget, 2) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-gray-500">Contributions</p>
                    <p class="font-bold text-gray-900 text-lg">${{ number_format($completion->total_contributions, 2) }}</p>
                    <p class="text-xs text-gray-400">received</p>
                </div>
            </div>
            @if($completion->organizer_notes)
            <div class="bg-yellow-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">{{ $completion->organizer_notes }}</p>
            </div>
            @endif
        </div>
        @else
        {{-- Complete form --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <p class="text-gray-600 text-sm mb-6">Mark this event as completed. This will generate a final summary report.</p>
            <form method="POST" action="{{ route('events.completion.store', $event) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organizer Notes (optional)</label>
                    <textarea name="organizer_notes" rows="4" placeholder="How did the event go? Any notes for next time..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">{{ old('organizer_notes') }}</textarea>
                </div>
                <button type="submit"
                        onclick="return confirm('Mark this event as completed?')"
                        class="w-full py-3 bg-green-600 hover:bg-green-500 text-white rounded-lg font-medium transition">
                    ✅ Mark as Completed
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
