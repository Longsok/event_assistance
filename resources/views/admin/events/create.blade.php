<x-admin-layout title="Create Event">
    <div class="mb-6">
        <a href="{{ route('admin.events.index') }}" class="text-indigo-400 text-sm hover:text-indigo-300">
            &larr; Back to Events
        </a>
    </div>

    <div class="max-w-2xl rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
        <h2 class="text-lg font-semibold mb-1" style="color:var(--text-strong)">Create Event</h2>
        <p class="text-sm mb-6" style="color:var(--text-soft)">Create an event on behalf of an organizer.</p>

        <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Organizer *</label>
                <select name="user_id" required
                        class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    <option value="">Select organizer...</option>
                    @foreach($organizers as $organizer)
                    <option value="{{ $organizer->id }}" {{ old('user_id') == $organizer->id ? 'selected' : '' }}>
                        {{ $organizer->name }} ({{ $organizer->email }})
                    </option>
                    @endforeach
                </select>
                @error('user_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Event Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Category *</label>
                    <select name="category_id" required
                            class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                            style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                        <option value="">Select category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    @error('start_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    @error('end_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue') }}"
                           class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                </div>

                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Capacity *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1" required
                           class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                           style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    @error('capacity')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Venue Type *</label>
                    <select name="venue_type" required
                            class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                            style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                        @foreach(['indoor' => 'Indoor', 'outdoor' => 'Outdoor', 'hybrid' => 'Hybrid'] as $v => $l)
                        <option value="{{ $v }}" {{ old('venue_type') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Status</label>
                    <select name="status"
                            class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                            style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                        @foreach(['draft' => 'Draft', 'published' => 'Published', 'ongoing' => 'Ongoing'] as $v => $l)
                        <option value="{{ $v }}" {{ old('status', 'draft') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500"
                              style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="meal_provided" id="meal_provided" value="1"
                           {{ old('meal_provided') ? 'checked' : '' }}
                           class="rounded" style="accent-color:#4f46e5">
                    <label for="meal_provided" class="text-sm" style="color:var(--text-soft)">Meal Provided</label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Create Event
                </button>
                <a href="{{ route('admin.events.index') }}"
                   class="px-5 py-2.5 rounded-lg text-sm transition text-center"
                   style="background:var(--input-bg);color:var(--text-soft)"
                   onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='var(--input-bg)'">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>