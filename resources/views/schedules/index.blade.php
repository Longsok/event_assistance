<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-sm hover:underline" style="color:#818cf8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-2xl font-bold mt-1" style="color:var(--text-strong)">Event Schedule</h2>
        <p class="text-sm mt-0.5" style="color:var(--text-soft)">Add and manage sessions for each day of your event.</p>
    </div>

    {{-- Add Session form --}}
    <div class="rounded-2xl border p-5" style="background:var(--panel);border-color:var(--border)">
        <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Add Session</h3>

        @if($errors->any())
        <div class="mb-4 p-3 rounded-xl text-sm" style="background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.2)">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('events.schedule.store', $event) }}"
              class="grid grid-cols-1 sm:grid-cols-2 gap-4"
              onsubmit="syncDuration()">
            @csrf

            <div class="sm:col-span-2">
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Session Name *</label>
                <input type="text" name="session_name" value="{{ old('session_name') }}" required
                       placeholder="e.g. Opening Ceremony"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>

            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Day *</label>
                <select name="day_number" required
                        class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    @php $totalDays = $event->start_date->diffInDays($event->end_date) + 1; @endphp
                    @for($d = 1; $d <= $totalDays; $d++)
                    <option value="{{ $d }}" {{ old('day_number') == $d ? 'selected' : '' }} style="background:var(--panel)">
                        Day {{ $d }} — {{ $event->start_date->copy()->addDays($d - 1)->format('D, M d') }}
                    </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Location</label>
                <input type="text" name="location" value="{{ old('location') }}"
                       placeholder="Optional"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>

            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Start Time *</label>
                <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                       onchange="syncDuration()"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>

            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">End Time *</label>
                <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required
                       onchange="syncDuration()"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>

            {{-- duration is auto-computed from start/end; hidden so the controller's required rule is satisfied --}}
            <input type="hidden" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 30) }}">

            <div class="sm:col-span-2">
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Notes</label>
                <input type="text" name="notes" value="{{ old('notes') }}"
                       placeholder="Optional notes..."
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>

            <div class="sm:col-span-2 flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer text-sm" style="color:var(--text-soft)">
                    <input type="checkbox" name="is_break" value="1" {{ old('is_break') ? 'checked' : '' }}
                           class="rounded" style="accent-color:#4f46e5">
                    This is a break
                </label>
            </div>

            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2.5 text-white text-sm font-medium rounded-xl"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    + Add Session
                </button>
            </div>
        </form>
    </div>

    {{-- Existing sessions by day --}}
    @foreach($schedulesByDay as $day => $sessions)
    <div class="rounded-2xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <div class="px-5 py-3" style="background:rgba(99,102,241,.08);border-bottom:1px solid var(--border)">
            <h3 class="font-semibold text-indigo-300">
                Day {{ $day }} — {{ $event->start_date->copy()->addDays($day - 1)->format('l, M d') }}
            </h3>
        </div>
        @foreach($sessions as $session)
        <div class="flex items-start gap-4 px-5 py-4" style="border-bottom:1px solid var(--border-soft);{{ ($session->is_break ?? false) ? 'background:var(--hover)' : '' }}">
            <div class="text-sm w-28 flex-shrink-0 font-mono" style="color:var(--text-soft)">
                {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium" style="color:var(--text-strong)">{{ $session->session_name }}</p>
                @if($session->location)
                <p class="text-xs mt-0.5" style="color:var(--text-soft)">📍 {{ $session->location }}</p>
                @endif
                @if($session->notes)
                <p class="text-xs mt-0.5" style="color:var(--text-soft)">{{ $session->notes }}</p>
                @endif
            </div>
            @if($session->duration_minutes)
            <span class="text-xs flex-shrink-0" style="color:var(--text-soft)">{{ $session->duration_minutes }}min</span>
            @endif
            <form method="POST" action="{{ route('events.schedule.destroy', [$event, $session]) }}" onsubmit="return confirm('Delete session?')">
                @csrf @method('DELETE')
                <button class="text-xs p-1 rounded transition" style="color:var(--text-soft)"
                        onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='var(--text-soft)'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endforeach

    @if($schedulesByDay->isEmpty())
    <div class="rounded-2xl border py-16 text-center" style="background:var(--panel);border-color:var(--border);color:var(--text-soft)">
        <svg class="w-10 h-10 mx-auto mb-3" style="color:var(--text-soft)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        No sessions yet. Add your first session above.
    </div>
    @endif
</div>

<script>
    // Auto-calculate duration_minutes from start/end time so the organizer
    // doesn't have to enter it (the controller requires it).
    function syncDuration() {
        const s = document.getElementById('start_time').value;
        const e = document.getElementById('end_time').value;
        const dur = document.getElementById('duration_minutes');
        if (!s || !e) return;
        const [sh, sm] = s.split(':').map(Number);
        const [eh, em] = e.split(':').map(Number);
        let mins = (eh * 60 + em) - (sh * 60 + sm);
        if (mins <= 0) mins = 30; // fallback if end is before start
        dur.value = mins;
    }
</script>
</x-app-layout>