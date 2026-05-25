<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Events
        </a>
        <h2 class="text-2xl font-bold text-white">Create New Event</h2>
        <p class="style="color:#6b7280" text-sm mt-1">Fill in the details — AI will suggest venues, vendors and budget for Phnom Penh.</p>
    </div>

    <form method="POST" action="{{ route('events.store') }}" class="space-y-6">
        @csrf

        {{-- Step indicator --}}
        <div class="flex items-center gap-3 mb-2">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</div>
                <span class="text-sm font-medium text-indigo-600">Event Details</span>
            </div>
            <div class="flex-1 h-px bg-slate-200"></div>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-slate-200 style="color:#6b7280" rounded-full flex items-center justify-center text-xs font-bold">2</div>
                <span class="text-sm style="color:#6b7280"">AI Suggestions</span>
            </div>
        </div>

        {{-- Basic Info --}}
        <div class="rounded-2xl border" style="background:#0d1117;border-color:rgba(255,255,255,.07) p-6 space-y-5">
            <h3 class="font-semibold text-slate-800 text-base">Basic Information</h3>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">Event Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Sokna & Sokjenn Wedding Ceremony"
                       class="w-full rounded-xl px-3 py-2.5 text-sm
                              focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:ring-1 focus:ring-indigo-100
                              @error('title') border-red-400 @enderror">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">Event Type *</label>
                <select name="category_id" required
                        class="w-full rounded-xl px-3 py-2.5 text-sm bg-white
                               focus:outline-none focus:ring-1 focus:ring-indigo-500
                               @error('category_id') border-red-400 @enderror">
                    <option value="">Select event type...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-1 focus:ring-indigo-500
                                  @error('start_date') border-red-400 @enderror">
                    @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-1 focus:ring-indigo-500
                                  @error('end_date') border-red-400 @enderror">
                    @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Times --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                           class="w-full rounded-xl px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                           class="w-full rounded-xl px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Capacity + Budget --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">Expected Guests *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1" required
                           class="w-full rounded-xl px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-1 focus:ring-indigo-500
                                  @error('capacity') border-red-400 @enderror">
                    @error('capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">
                        Total Budget (USD)
                        <span class="style="color:#6b7280" font-normal">— used for AI suggestions</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 style="color:#6b7280" text-sm">$</span>
                        <input type="number" name="total_budget" value="{{ old('total_budget') }}"
                               min="0" step="100" placeholder="e.g. 5000"
                               class="w-full pl-7 border border-slate-300 rounded-xl px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">Description</label>
                <textarea name="description" rows="3" placeholder="Brief description of the event..."
                          class="w-full rounded-xl px-3 py-2.5 text-sm
                                 focus:outline-none focus:ring-1 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Venue Preferences --}}
        <div class="rounded-2xl border" style="background:#0d1117;border-color:rgba(255,255,255,.07) p-6 space-y-5">
            <div>
                <h3 class="font-semibold text-slate-800 text-base">Venue Preferences</h3>
                <p class="style="color:#6b7280" text-xs mt-0.5">These help AI suggest the right venues in Phnom Penh</p>
            </div>

            {{-- Venue Type --}}
            <div>
                <label class="block text-sm font-medium style="color:#9ca3af" mb-2">Venue Setting *</label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['indoor' => '🏛️ Indoor', 'outdoor' => '🌿 Outdoor', 'hybrid' => '✨ Hybrid'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="venue_type" value="{{ $val }}" class="sr-only peer"
                               {{ old('venue_type', 'indoor') === $val ? 'checked' : '' }}>
                        <div class="border-2 border-slate-200 rounded-xl px-3 py-2.5 text-sm text-center
                                    font-medium transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                                    peer-checked:text-indigo-700 hover:border-slate-300 text-slate-600">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Style Preference --}}
            <div>
                <label class="block text-sm font-medium style="color:#9ca3af" mb-2">Event Style</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach(['modern' => '💎 Modern', 'traditional' => '🏮 Traditional', 'formal' => '🎩 Formal', 'casual' => '🎉 Casual'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="style_pref" value="{{ $val }}" class="sr-only peer"
                               {{ old('style_pref', 'modern') === $val ? 'checked' : '' }}>
                        <div class="border-2 border-slate-200 rounded-xl px-3 py-2.5 text-sm text-center
                                    font-medium transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50
                                    peer-checked:text-indigo-700 hover:border-slate-300 text-slate-600">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Venue (manual, optional) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">
                        Venue Name
                        <span class="style="color:#6b7280" font-normal">— or let AI suggest</span>
                    </label>
                    <input type="text" name="venue" value="{{ old('venue') }}"
                           placeholder="Leave blank for AI recommendations"
                           class="w-full rounded-xl px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium style="color:#9ca3af" mb-1.5">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           placeholder="e.g. Phnom Penh, Cambodia"
                           class="w-full rounded-xl px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Event Settings --}}
        <div class="rounded-2xl border" style="background:#0d1117;border-color:rgba(255,255,255,.07) p-6 space-y-4">
            <h3 class="font-semibold text-slate-800 text-base">Event Settings</h3>

            {{-- Meal --}}
            <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative">
                    <input type="checkbox" name="meal_provided" value="1"
                           {{ old('meal_provided') ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-6 bg-slate-200 peer-checked:bg-indigo-600 rounded-full transition"></div>
                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition
                                peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-medium style="color:#9ca3af"">Meal Provided</p>
                    <p class="text-xs style="color:#6b7280"">Include catering in schedule and budget</p>
                </div>
            </label>

            {{-- Public event --}}
            <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative">
                    <input type="checkbox" name="is_public" value="1"
                           {{ old('is_public') ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-6 bg-slate-200 peer-checked:bg-indigo-600 rounded-full transition"></div>
                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition
                                peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-medium style="color:#9ca3af"">Public Event</p>
                    <p class="text-xs style="color:#6b7280"">Anyone can check in via QR code — no invite required (Grand Opening, Conference, etc.)</p>
                </div>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3.5 text-white font-semibold rounded-xl" style="background:linear-gradient(135deg,#4f46e5,#7c3aed) text-white font-semibold
                       rounded-2xl transition text-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Create Event & Get AI Recommendations
        </button>
        <p class="text-center text-xs style="color:#6b7280"">
            AI will suggest real venues, caterers and decor companies in Phnom Penh based on your inputs.
        </p>
    </form>
</div>
</x-app-layout>
