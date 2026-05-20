# Run from project root:
# powershell -ExecutionPolicy Bypass -File create_livewire_views.ps1

Write-Host "Creating Livewire views..." -ForegroundColor Cyan

New-Item -ItemType Directory -Force -Path "resources\views\livewire" | Out-Null

# ── 1. attendance-counter.blade.php ──────────────────────────────────────────
Set-Content "resources\views\livewire\attendance-counter.blade.php" @'
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $stats['checked_in'] ?? 0 }}</p>
        <p class="text-sm text-gray-500 mt-1">Checked In</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-gray-900">{{ $stats['expected'] ?? 0 }}</p>
        <p class="text-sm text-gray-500 mt-1">Expected</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        @php
            $rate = ($stats['expected'] ?? 0) > 0
                ? round((($stats['checked_in'] ?? 0) / $stats['expected']) * 100)
                : 0;
        @endphp
        <p class="text-3xl font-bold text-indigo-600">{{ $rate }}%</p>
        <p class="text-sm text-gray-500 mt-1">Attendance Rate</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-orange-500">
            {{ ($stats['expected'] ?? 0) - ($stats['checked_in'] ?? 0) }}
        </p>
        <p class="text-sm text-gray-500 mt-1">Not Arrived</p>
    </div>

    {{-- Live indicator --}}
    <div class="col-span-2 lg:col-span-4 flex items-center justify-center gap-2 text-sm text-green-600">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        Live — updates every 3 seconds
    </div>
</div>
'@

# ── 2. budget-tracker.blade.php ───────────────────────────────────────────────
Set-Content "resources\views\livewire\budget-tracker.blade.php" @'
<div class="space-y-4">

    {{-- Summary Bar --}}
    @if($summary['has_budget'])
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex flex-wrap gap-4 mb-4">
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Total Budget</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($summary['total_budget'], 2) }}</p>
            </div>
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Allocated</p>
                <p class="text-xl font-bold text-indigo-600">${{ number_format($summary['total_allocated'], 2) }}</p>
            </div>
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Actual Spent</p>
                <p class="text-xl font-bold {{ $summary['over_budget'] ? 'text-red-600' : 'text-green-600' }}">
                    ${{ number_format($summary['total_actual'], 2) }}
                </p>
            </div>
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Remaining</p>
                <p class="text-xl font-bold {{ $summary['unallocated'] < 0 ? 'text-red-600' : 'text-gray-700' }}">
                    ${{ number_format($summary['unallocated'], 2) }}
                </p>
            </div>
        </div>

        {{-- Progress bar --}}
        @php
            $spentPct = $summary['total_budget'] > 0
                ? min(100, round(($summary['total_actual'] / $summary['total_budget']) * 100))
                : 0;
        @endphp
        <div class="bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="h-full rounded-full transition-all {{ $summary['over_budget'] ? 'bg-red-500' : 'bg-green-500' }}"
                 style="width: {{ $spentPct }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1 text-right">{{ $spentPct }}% spent</p>
    </div>

    {{-- Budget Items — inline editable --}}
    @if($budget && $budget->items->count())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
            <p class="text-sm font-semibold text-gray-700">Budget Items</p>
        </div>
        @foreach($budget->items as $item)
        <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-100 last:border-0
                    {{ $item->isOverBudget() ? 'bg-red-50' : 'hover:bg-gray-50' }} transition">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $item->line_item }}</p>
                <p class="text-xs text-gray-400">Allocated: ${{ number_format($item->allocated_amount, 2) }}</p>
            </div>

            {{-- Inline actual amount edit --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Actual $</span>
                <input type="number"
                       value="{{ $item->actual_amount }}"
                       min="0" step="0.01"
                       wire:change="updateActual({{ $item->id }}, $event.target.value)"
                       class="w-24 border border-gray-300 rounded-lg px-2 py-1.5 text-sm text-right
                              focus:outline-none focus:border-indigo-400
                              {{ $item->isOverBudget() ? 'border-red-300 bg-red-50' : '' }}">
            </div>

            @if($item->isOverBudget())
            <span class="text-xs text-red-500 font-medium flex-shrink-0">Over!</span>
            @else
            <span class="text-xs text-green-600 flex-shrink-0">${{ number_format($item->remaining, 2) }} left</span>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @else
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center text-gray-400">
        <p class="text-3xl mb-2">💰</p>
        <p>No budget set yet.</p>
    </div>
    @endif
</div>
'@

# ── 3. guest-search.blade.php ─────────────────────────────────────────────────
Set-Content "resources\views\livewire\guest-search.blade.php" @'
<div class="space-y-4">

    {{-- Search & Filter --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search guests by name or email..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:border-indigo-400">
            </div>
            <select wire:model.live="rsvpFilter"
                    class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm
                           focus:outline-none focus:border-indigo-400 bg-white">
                <option value="">All RSVP</option>
                <option value="confirmed">✅ Confirmed</option>
                <option value="pending">⏳ Pending</option>
                <option value="declined">❌ Declined</option>
                <option value="attended">🎟️ Attended</option>
            </select>
            @if($search || $rsvpFilter)
            <button wire:click="$set('search', ''); $set('rsvpFilter', '')"
                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm rounded-xl transition">
                Clear
            </button>
            @endif
        </div>
    </div>

    {{-- Results --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-700">Guests</p>
            <p class="text-xs text-gray-400">{{ $guests->total() }} results</p>
        </div>

        @forelse($guests as $eg)
        <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center
                        text-indigo-700 font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr($eg->guest->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 truncate">{{ $eg->guest->name ?? '-' }}</p>
                <p class="text-xs text-gray-400">
                    {{ $eg->guest->email ?? 'No email' }}
                    @if($eg->guest_code) · {{ $eg->guest_code }} @endif
                </p>
            </div>
            <div class="text-right flex-shrink-0">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    {{ $eg->rsvp_status === 'confirmed' ? 'bg-green-100 text-green-700' :
                       ($eg->rsvp_status === 'declined'  ? 'bg-red-100 text-red-700' :
                       ($eg->rsvp_status === 'attended'  ? 'bg-blue-100 text-blue-700' :
                        'bg-gray-100 text-gray-600')) }}">
                    {{ ucfirst($eg->rsvp_status) }}
                </span>
                @if($eg->isCheckedIn())
                <p class="text-xs text-green-600 mt-1">✅ Checked in</p>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-gray-400">
            <p class="text-3xl mb-2">🔍</p>
            <p>No guests found{{ $search ? ' for "' . $search . '"' : '' }}.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>{{ $guests->links() }}</div>
</div>
'@

# ── 4. task-checklist.blade.php ───────────────────────────────────────────────
Set-Content "resources\views\livewire\task-checklist.blade.php" @'
<div class="space-y-4">

    {{-- Progress --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold text-gray-800">Task Progress</p>
            <p class="text-sm text-gray-500">
                <span class="font-bold text-indigo-600">{{ $progress['completed'] }}</span>
                / {{ $progress['total'] }} completed
            </p>
        </div>
        @php $pct = $progress['total'] > 0 ? round(($progress['completed'] / $progress['total']) * 100) : 0; @endphp
        <div class="bg-gray-100 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full bg-indigo-600 transition-all duration-500"
                 style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1.5 text-right">{{ $pct }}% done</p>
    </div>

    {{-- Task Groups --}}
    @foreach($groups as $group)
    @if($group->eventTasks->count())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Group Header — click to expand/collapse --}}
        <button wire:click="toggleGroup({{ $group->id }})"
                class="w-full flex items-center gap-3 px-5 py-4 border-b border-gray-100
                       hover:bg-gray-50 transition text-left">
            <div class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $group->color }}"></div>
            <span class="font-semibold text-gray-800 flex-1">{{ $group->name }}</span>
            <span class="text-xs text-gray-400">
                {{ $group->eventTasks->where('status','done')->count() }}/{{ $group->eventTasks->count() }}
            </span>
            <svg class="w-4 h-4 text-gray-400 transition-transform {{ in_array($group->id, $expandedGroups) ? 'rotate-180' : '' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Tasks --}}
        @if(in_array($group->id, $expandedGroups))
        @foreach($group->eventTasks as $task)
        <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-100 last:border-0
                    {{ $task->status === 'overdue' ? 'bg-red-50' : 'hover:bg-gray-50' }} transition">

            {{-- Toggle button --}}
            <button wire:click="toggleTask({{ $task->id }})"
                    class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition
                           {{ $task->status === 'done'
                               ? 'bg-green-500 border-green-500 text-white'
                               : ($task->status === 'overdue'
                                   ? 'border-red-400 hover:border-red-600'
                                   : 'border-gray-300 hover:border-indigo-500') }}">
                @if($task->status === 'done')
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                @endif
            </button>

            {{-- Task info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate
                          {{ $task->status === 'done' ? 'line-through text-gray-400' : 'text-gray-800' }}">
                    {{ $task->task_name }}
                </p>
                @if($task->late_note)
                <p class="text-xs text-red-500 truncate mt-0.5">⚠️ {{ $task->late_note }}</p>
                @elseif($task->notes)
                <p class="text-xs text-gray-400 truncate mt-0.5">{{ $task->notes }}</p>
                @endif
            </div>

            {{-- Priority badge --}}
            <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0
                {{ $task->priority === 'high'   ? 'bg-red-100 text-red-600' :
                   ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-600' :
                    'bg-gray-100 text-gray-500') }}">
                {{ $task->priority }}
            </span>

            {{-- Due date --}}
            <span class="text-xs flex-shrink-0
                {{ $task->status === 'overdue' ? 'text-red-500 font-semibold' :
                   ($task->status === 'done'   ? 'text-gray-300' : 'text-gray-400') }}">
                {{ $task->due_date->format('M d') }}
            </span>
        </div>
        @endforeach
        @endif

    </div>
    @endif
    @endforeach

    @if($groups->isEmpty() || $groups->every(fn($g) => $g->eventTasks->isEmpty()))
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-10 text-center text-gray-400">
        <p class="text-4xl mb-3">✅</p>
        <p>No tasks yet. Tasks are auto-generated when you create an event with a category.</p>
    </div>
    @endif
</div>
'@

# ── 5. timeline-warning.blade.php ────────────────────────────────────────────
Set-Content "resources\views\livewire\timeline-warning.blade.php" @'
<div>
    @if($showWarning && count($preview['overdue_tasks'] ?? []) > 0)
    <div class="mt-4 bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <span class="text-xl flex-shrink-0">⚠️</span>
            <div class="flex-1">
                <p class="font-semibold text-red-700 text-sm">
                    {{ $preview['overdue_count'] }} task{{ $preview['overdue_count'] > 1 ? 's' : '' }}
                    already overdue for this event date
                </p>
                <p class="text-xs text-red-600 mt-1 mb-3">
                    These tasks will be marked as overdue and due today when you create the event.
                </p>
                <div class="space-y-1">
                    @foreach(array_slice($preview['overdue_tasks'], 0, 5) as $task)
                    <div class="flex items-center gap-2 text-xs text-red-600">
                        <span class="w-1.5 h-1.5 bg-red-400 rounded-full flex-shrink-0"></span>
                        <span class="font-medium">{{ $task['task_name'] }}</span>
                        <span class="text-red-400">— was due {{ $task['was_due'] }}</span>
                    </div>
                    @endforeach
                    @if($preview['overdue_count'] > 5)
                    <p class="text-xs text-red-400">+ {{ $preview['overdue_count'] - 5 }} more...</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @elseif(!empty($preview) && ($preview['ontrack_count'] ?? 0) > 0)
    <div class="mt-4 bg-green-50 border border-green-200 rounded-2xl p-4">
        <div class="flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="text-sm text-green-700 font-medium">
                All {{ $preview['ontrack_count'] }} tasks are on track for this date.
            </p>
        </div>
    </div>
    @endif
</div>
'@

Write-Host ""
Write-Host "✅ All 5 Livewire views created!" -ForegroundColor Green
Write-Host ""
Write-Host "Run: php artisan optimize:clear" -ForegroundColor Yellow
