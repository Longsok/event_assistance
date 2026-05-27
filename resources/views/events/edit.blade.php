<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-sm hover:underline mb-3" style="color:#818cf8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-2xl font-bold text-white">Edit Event</h2>
    </div>

    <style>
        .dark-input {
            width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
            border-radius:12px;padding:10px 14px;color:white;font-size:14px;outline:none;
            font-family:inherit;transition:border-color .15s;
        }
        .dark-input:focus { border-color:rgba(99,102,241,.5); }
        .dark-input::placeholder { color:#4b5563; }
        .dark-input option { background:#0d1117;color:white; }
    </style>

    <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-6">
        @csrf @method('PATCH')

        {{-- Basic Information --}}
        <div class="rounded-2xl border p-6 space-y-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <h3 class="font-semibold text-white">Basic Information</h3>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Event Title *</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                       class="dark-input" placeholder="Event title">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Event Type *</label>
                <select name="category_id" required class="dark-input" style="background:#0d1117;cursor:pointer">
                    <option value="" style="background:#0d1117;color:#6b7280">Select event type...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            style="background:#0d1117;color:white"
                            {{ old('category_id', $event->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Start Date *</label>
                    <input type="date" name="start_date"
                           value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required
                           class="dark-input" style="color-scheme:dark">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">End Date *</label>
                    <input type="date" name="end_date"
                           value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required
                           class="dark-input" style="color-scheme:dark">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}"
                           class="dark-input" style="color-scheme:dark">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}"
                           class="dark-input" style="color-scheme:dark">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Capacity *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1" required
                           class="dark-input">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue', $event->venue) }}"
                           placeholder="Venue name" class="dark-input">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Address</label>
                <input type="text" name="address" value="{{ old('address', $event->address) }}"
                       class="dark-input" placeholder="e.g. Phnom Penh, Cambodia">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color:#9ca3af">Venue Setting *</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['indoor'=>'🏛️ Indoor','outdoor'=>'🌿 Outdoor','hybrid'=>'✨ Hybrid'] as $val=>$label)
                    <label class="cursor-pointer">
                        <input type="radio" name="venue_type" value="{{ $val }}" class="sr-only peer"
                               {{ old('venue_type', $event->venue_type) === $val ? 'checked' : '' }}>
                        <div class="peer-checked:!border-indigo-500 peer-checked:!text-indigo-300 transition"
                             style="border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px;font-size:13px;font-weight:500;text-align:center;color:#6b7280;background:rgba(255,255,255,.03)">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Description</label>
                <textarea name="description" rows="3" placeholder="Brief description..."
                          class="dark-input resize-none">{{ old('description', $event->description) }}</textarea>
            </div>
        </div>

        {{-- ── Event Settings (status + public toggle) ── --}}
        <div class="rounded-2xl border p-6 space-y-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <h3 class="font-semibold text-white">Event Settings</h3>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Status</label>
                <select name="status" class="dark-input" style="background:#0d1117;cursor:pointer">
                    @foreach(['draft'=>'Draft','published'=>'Published','ongoing'=>'Ongoing','completed'=>'Completed','cancelled'=>'Cancelled'] as $val=>$label)
                    <option value="{{ $val }}"
                            style="background:#0d1117;color:white"
                            {{ old('status', $event->status) === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                <p class="text-xs mt-1.5" style="color:#6b7280">
                    Set to <strong style="color:#a5b4fc">Published</strong> to make the event visible to guests.
                </p>
            </div>

            {{-- is_public toggle --}}
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative flex-shrink-0">
                    <input type="checkbox" name="is_public" value="1"
                           {{ old('is_public', $event->is_public) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-6 rounded-full transition peer-checked:bg-indigo-600"
                         style="background:rgba(255,255,255,.1)"></div>
                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition
                                peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Public Event</p>
                    <p class="text-xs" style="color:#6b7280">
                        Anyone can check in via QR code — no invite required (Grand Opening, Conference, etc.)
                    </p>
                </div>
            </label>

            {{-- meal_provided toggle --}}
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative flex-shrink-0">
                    <input type="checkbox" name="meal_provided" value="1"
                           {{ old('meal_provided', $event->meal_provided) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-6 rounded-full transition peer-checked:bg-indigo-600"
                         style="background:rgba(255,255,255,.1)"></div>
                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition
                                peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Meal Provided</p>
                    <p class="text-xs" style="color:#6b7280">Include catering in schedule and budget</p>
                </div>
            </label>
        </div>

        @if($errors->any())
        <div class="rounded-xl p-4" style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2)">
            @foreach($errors->all() as $error)
            <p class="text-sm" style="color:#f87171">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 py-3 text-white font-semibold rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                Save Changes
            </button>
            <a href="{{ route('events.show', $event) }}"
               class="px-6 py-3 text-sm font-medium rounded-xl flex items-center"
               style="background:rgba(255,255,255,.07);color:#9ca3af">
                Cancel
            </a>
        </div>
    </form>
</div>
</x-app-layout>
