<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-sm hover:underline mb-3" style="color:#818cf8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-2xl font-bold text-white">Edit Event</h2>
    </div>

    <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-6">
        @csrf @method('PATCH')

        <div class="rounded-2xl border p-6 space-y-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
            <h3 class="font-semibold text-white">Basic Information</h3>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Event Title *</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                       class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Event Type *</label>
                <select name="category_id" required
                        class="w-full rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    <option value="">Select event type...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $event->category_id)==$cat->id ? 'selected' : '' }}
                            style="background:#0d1117">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color-scheme:dark">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color-scheme:dark">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}"
                           class="w-full rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color-scheme:dark">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}"
                           class="w-full rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color-scheme:dark">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Capacity *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" placeholder="Venue name"
                           class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Address</label>
                <input type="text" name="address" value="{{ old('address', $event->address) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color:#9ca3af">Venue Setting *</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['indoor'=>'🏛️ Indoor','outdoor'=>'🌿 Outdoor','hybrid'=>'✨ Hybrid'] as $val=>$label)
                    <label class="cursor-pointer">
                        <input type="radio" name="venue_type" value="{{ $val }}" class="sr-only peer"
                               {{ old('venue_type',$event->venue_type)===$val?'checked':'' }}>
                        <div class="border rounded-xl px-3 py-2.5 text-sm text-center font-medium transition text-gray-400
                                    peer-checked:text-indigo-300"
                             style="border-color:rgba(255,255,255,.1);background:rgba(255,255,255,.04)"
                             :class="...">{{ $label }}</div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Description</label>
                <textarea name="description" rows="3" placeholder="Brief description..."
                          class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 resize-none"
                          style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">{{ old('description',$event->description) }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 py-3 text-white font-semibold rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">Save Changes</button>
            <a href="{{ route('events.show', $event) }}"
               class="px-6 py-3 text-sm font-medium rounded-xl"
               style="background:rgba(255,255,255,.07);color:#9ca3af">Cancel</a>
        </div>
    </form>
</div>
</x-app-layout>
