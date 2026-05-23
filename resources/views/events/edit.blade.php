<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Event
        </a>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-6">Edit: {{ $event->title }}</h2>
        <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Event Title *</label>
                    <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                           class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Category *</label>
                    <select name="category_id" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $event->category_id)==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required
                           class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required
                           class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue', $event->venue) }}"
                           class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Capacity *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1" required
                           class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Venue Type *</label>
                    <select name="venue_type" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                        @foreach(['indoor','outdoor','hybrid'] as $vt)
                        <option value="{{ $vt }}" {{ old('venue_type', $event->venue_type)===$vt ? 'selected' : '' }}>{{ ucfirst($vt) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">{{ old('description', $event->description) }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-medium transition">Update Event</button>
                <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Delete this event? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-3 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-xl text-sm font-medium transition">Delete</button>
                </form>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
