<x-admin-layout title="Create Event">
    <div class="mb-6">
        <a href="{{ route('admin.events.index') }}" class="text-indigo-400 text-sm hover:text-indigo-300">
            &larr; Back to Events
        </a>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-800 p-6" style="background:#111827">
        <h2 class="text-lg font-semibold text-white mb-1">Create Event</h2>
        <p class="text-sm text-gray-500 mb-6">Create an event on behalf of an organizer.</p>

        <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Organizer *</label>
                <select name="user_id" required
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                               focus:outline-none focus:border-indigo-500">
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
                    <label class="block text-sm text-gray-400 mb-1.5">Event Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                  focus:outline-none focus:border-indigo-500">
                    @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm text-gray-400 mb-1.5">Category *</label>
                    <select name="category_id" required
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                   focus:outline-none focus:border-indigo-500">
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
                    <label class="block text-sm text-gray-400 mb-1.5">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                  focus:outline-none focus:border-indigo-500">
                    @error('start_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                  focus:outline-none focus:border-indigo-500">
                    @error('end_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Venue</label>
                    <input type="text" name="venue" value="{{ old('venue') }}"
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                  focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Capacity *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                  focus:outline-none focus:border-indigo-500">
                    @error('capacity')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Venue Type *</label>
                    <select name="venue_type" required
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                   focus:outline-none focus:border-indigo-500">
                        @foreach(['indoor' => 'Indoor', 'outdoor' => 'Outdoor', 'hybrid' => 'Hybrid'] as $v => $l)
                        <option value="{{ $v }}" {{ old('venue_type') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-400 mb-1.5">Status</label>
                    <select name="status"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                   focus:outline-none focus:border-indigo-500">
                        @foreach(['draft' => 'Draft', 'published' => 'Published', 'ongoing' => 'Ongoing'] as $v => $l)
                        <option value="{{ $v }}" {{ old('status', 'draft') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm text-gray-400 mb-1.5">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                                     focus:outline-none focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="meal_provided" id="meal_provided" value="1"
                           {{ old('meal_provided') ? 'checked' : '' }}
                           class="rounded border-gray-600 bg-gray-800 text-indigo-600 focus:ring-indigo-500">
                    <label for="meal_provided" class="text-sm text-gray-400">Meal Provided</label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Create Event
                </button>
                <a href="{{ route('admin.events.index') }}"
                   class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
