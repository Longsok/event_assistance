# Run from project root:
# powershell -ExecutionPolicy Bypass -File integrate_livewire.ps1

Write-Host "Integrating Livewire components into blade files..." -ForegroundColor Cyan

# ── 1. attendance/index.blade.php — replace stats section with livewire counter
$attendance = Get-Content "resources\views\attendance\index.blade.php" -Raw
$attendance = $attendance -replace '(\{\{-- Stats --\}\}.*?</div>\s*</div>\s*</div>)', '<livewire:attendance-counter :event="$event" />'
# Simpler: just rewrite the whole file with livewire embedded
Set-Content "resources\views\attendance\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
                <h2 class="text-2xl font-bold text-gray-900 mt-1">Attendance</h2>
            </div>
            <span class="text-sm text-gray-500">{{ now()->format('M d, Y') }}</span>
        </div>

        {{-- Live Stats Counter (auto-updates every 3s) --}}
        <livewire:attendance-counter :event="$event" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- QR Code Panel --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                @if($event->attendance_token)
                    <div class="text-center space-y-4">
                        <div class="inline-flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm font-medium px-4 py-2 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Check-in is LIVE
                        </div>

                        @if($qrCode)
                        <div class="inline-block p-4 bg-white border-2 border-gray-100 rounded-2xl shadow-sm">
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                                 alt="Check-in QR Code"
                                 class="w-56 h-56">
                        </div>
                        @endif

                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs text-gray-400 mb-1">Check-in URL</p>
                            <p class="text-xs font-mono text-gray-600 break-all">
                                {{ route('public.checkin', $event->attendance_token) }}
                            </p>
                        </div>

                        <p class="text-sm text-gray-500">Guests scan this QR with their phone to check in.</p>

                        <form method="POST" action="{{ route('events.attendance.stop', $event) }}">
                            @csrf
                            <button class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-sm font-medium rounded-xl transition">
                                ⏹ Stop Check-in
                            </button>
                        </form>
                    </div>
                @else
                    <div class="text-center space-y-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Check-in Not Started</h3>
                            <p class="text-sm text-gray-500 mt-1">Start check-in to generate the QR code.</p>
                        </div>
                        <form method="POST" action="{{ route('events.attendance.start', $event) }}">
                            @csrf
                            <button class="w-full py-3 bg-green-600 hover:bg-green-500 text-white font-medium rounded-xl transition">
                                ▶ Start Check-in
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Manual Check-in --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Manual Check-in</h3>
                <form method="POST" action="{{ route('events.attendance.manual', $event) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Select Guest</label>
                        <select name="event_guest_id"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                            <option value="">Choose guest...</option>
                            @foreach($event->eventGuests()->with('guest')->get() as $eg)
                            <option value="{{ $eg->id }}">
                                {{ $eg->guest->name }} — {{ $eg->guest_code }}
                                @if($eg->isCheckedIn()) ✅ @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                        Check In Manually
                    </button>
                </form>

                @if(session('success'))
                <div class="mt-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mt-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Check-in Log --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Check-in Log</h3>
                <span class="text-sm text-gray-400">{{ $checkedIn->count() }} arrived</span>
            </div>
            @forelse($checkedIn as $log)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($log->eventGuest->guest->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $log->eventGuest->guest->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $log->scan_method === 'self' ? '📱 Self check-in' : '👤 Manual' }}</p>
                </div>
                <p class="text-sm text-gray-500 font-medium">{{ $log->checked_in_at->format('H:i') }}</p>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-gray-400">
                <p class="text-4xl mb-3">👥</p>
                <p>No check-ins yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
'@

# ── 2. budget/index.blade.php — replace items table with livewire tracker
Set-Content "resources\views\budget\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-1">Budget Tracker</h2>
        </div>

        {{-- Set Total Budget --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Total Budget</h3>
            <form method="POST" action="{{ route('events.budget.update', $event) }}" class="flex items-end gap-4">
                @csrf @method('PATCH')
                <div class="flex-1">
                    <label class="block text-sm text-gray-500 mb-1">Amount ($)</label>
                    <input type="number" name="total_budget"
                           value="{{ $budget?->total_budget ?? 0 }}"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                    Update
                </button>
            </form>
        </div>

        {{-- Live Budget Tracker (inline editable actual amounts) --}}
        <livewire:budget-tracker :event="$event" />

        {{-- Add Custom Budget Item --}}
        @if($budget)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Add Budget Item</h3>
            <form method="POST" action="{{ route('events.budget.items.store', $event) }}"
                  class="flex flex-wrap gap-3">
                @csrf
                <div class="flex-1 min-w-48">
                    <input type="text" name="line_item" placeholder="Item name (e.g. Catering)"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                           required>
                </div>
                <div class="w-36">
                    <input type="number" name="allocated_amount" placeholder="Amount"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                           required>
                </div>
                <button type="submit"
                        class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-xl transition">
                    + Add Item
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
'@

# ── 3. guests/event-guests.blade.php — add livewire guest search
Set-Content "resources\views\guests\event-guests.blade.php" @'
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
'@

# ── 4. tasks/index.blade.php — replace with livewire checklist
Set-Content "resources\views\tasks\index.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
                <h2 class="text-2xl font-bold text-gray-900 mt-1">Task Checklist</h2>
            </div>
        </div>

        {{-- Live Task Checklist (Livewire — click to complete) --}}
        <livewire:task-checklist :event="$event" />

        {{-- Add Custom Task --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Add Custom Task</h3>
            <form method="POST" action="{{ route('events.tasks.store', $event) }}"
                  class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Task Name *</label>
                    <input type="text" name="task_name" placeholder="e.g. Confirm catering headcount"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                           required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Group *</label>
                    <select name="group_id"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                            required>
                        <option value="">Select group...</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Due Date *</label>
                    <input type="date" name="due_date"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                           required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Priority *</label>
                    <select name="priority"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Notes</label>
                    <input type="text" name="notes" placeholder="Optional notes..."
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">
                        + Add Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
'@

# ── 5. events/create.blade.php — add timeline warning to form
Set-Content "resources\views\events\create.blade.php" @'
<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('events.index') }}" class="text-indigo-600 text-sm hover:underline">&larr; Back to Events</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Create New Event</h2>
            <p class="text-sm text-gray-400 mb-6">Fill in the details — tasks, schedule and budget will be auto-generated.</p>

            <form method="POST" action="{{ route('events.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                               required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category_id"
                                wire:model="categoryId"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                                required>
                            <option value="">Select category...</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               wire:model="startDate"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                               required>
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                               required>
                        @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue') }}"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacity *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                               required>
                        @error('capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Venue Type *</label>
                        <select name="venue_type"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400"
                                required>
                            <option value="indoor"  {{ old('venue_type') === 'indoor'  ? 'selected' : '' }}>Indoor</option>
                            <option value="outdoor" {{ old('venue_type') === 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                            <option value="hybrid"  {{ old('venue_type') === 'hybrid'  ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Budget ($)</label>
                        <input type="number" name="total_budget" value="{{ old('total_budget') }}"
                               min="0" step="0.01"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="meal_provided" id="meal" value="1"
                               {{ old('meal_provided') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600">
                        <label for="meal" class="text-sm text-gray-700">Meal Provided</label>
                    </div>
                </div>

                {{-- Timeline Warning (live preview before submitting) --}}
                <livewire:timeline-warning />

                <button type="submit"
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium transition shadow-sm">
                    ✨ Create Event & Generate Timeline
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
'@

Write-Host ""
Write-Host "✅ Livewire components integrated into all pages!" -ForegroundColor Green
Write-Host ""
Write-Host "Summary of what's now live:" -ForegroundColor Cyan
Write-Host "  attendance/index  -> livewire:attendance-counter (updates every 3s)"
Write-Host "  budget/index      -> livewire:budget-tracker (inline edit actual amounts)"
Write-Host "  guests/event-guests -> livewire:guest-search (live search + RSVP filter)"
Write-Host "  tasks/index       -> livewire:task-checklist (click to complete)"
Write-Host "  events/create     -> livewire:timeline-warning (live overdue preview)"
Write-Host ""
Write-Host "Run: php artisan optimize:clear" -ForegroundColor Yellow
