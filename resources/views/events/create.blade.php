<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-1 text-sm hover:underline mb-3" style="color:#818cf8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Events
        </a>
        <h2 class="text-2xl font-bold text-white">Create New Event</h2>
        <p class="text-sm mt-1" style="color:#6b7280">Fill in the details — AI will suggest venues, vendors and budget for Phnom Penh.</p>
    </div>

    <form method="POST" action="{{ route('events.store') }}" class="space-y-6">
        @csrf

        {{-- Step indicator --}}
        <div class="flex items-center gap-3 mb-2">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white" style="background:#4f46e5">1</div>
                <span class="text-sm font-medium" style="color:#818cf8">Event Details</span>
            </div>
            <div class="flex-1 h-px" style="background:rgba(255,255,255,.1)"></div>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background:rgba(255,255,255,.08);color:#6b7280">2</div>
                <span class="text-sm" style="color:#6b7280">AI Suggestions</span>
            </div>
        </div>

        {{-- Shared input style via CSS --}}
        <style>
            .dark-input {
                width:100%;
                background:rgba(255,255,255,.06);
                border:1px solid rgba(255,255,255,.1);
                border-radius:12px;
                padding:10px 14px;
                color:white;
                font-size:14px;
                outline:none;
                font-family:inherit;
                transition:border-color .15s;
            }
            .dark-input:focus { border-color:rgba(99,102,241,.5); }
            .dark-input::placeholder { color:#4b5563; }
            .dark-input option {
                background:#0d1117;
                color:white;
            }
            .dark-label {
                display:block;
                font-size:13px;
                font-weight:500;
                color:#9ca3af;
                margin-bottom:6px;
            }
            .dark-card {
                background:#0d1117;
                border:1px solid rgba(255,255,255,.07);
                border-radius:16px;
                padding:24px;
            }
            .dark-card h3 {
                font-size:15px;
                font-weight:600;
                color:white;
                margin:0 0 20px;
            }
            .dark-radio-option {
                border:1px solid rgba(255,255,255,.1);
                border-radius:12px;
                padding:10px 12px;
                font-size:13px;
                font-weight:500;
                text-align:center;
                color:#6b7280;
                cursor:pointer;
                transition:all .15s;
                background:rgba(255,255,255,.03);
            }
            input[type=radio].sr-only:checked + .dark-radio-option {
                border-color:#4f46e5;
                background:rgba(79,70,229,.15);
                color:#a5b4fc;
            }
        </style>

        {{-- ── Basic Info ── --}}
        <div class="dark-card space-y-5">
            <h3>Basic Information</h3>

            {{-- Title --}}
            <div>
                <label class="dark-label">Event Title <span style="color:#f87171">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Sokna & Sokjenn Wedding Ceremony"
                       class="dark-input @error('title') !border-red-500 @enderror">
                @error('title')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
            </div>

            {{-- Category --}}
            <div>
                <label class="dark-label">Event Type <span style="color:#f87171">*</span></label>
                <select name="category_id" required
                        class="dark-input @error('category_id') !border-red-500 @enderror"
                        style="background:#0d1117;color:white;cursor:pointer">
                    <option value="" style="background:#0d1117;color:#6b7280">Select event type...</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            style="background:#0d1117;color:white"
                            {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="dark-label">Start Date <span style="color:#f87171">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="dark-input @error('start_date') !border-red-500 @enderror"
                           style="color-scheme:dark">
                    @error('start_date')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="dark-label">End Date <span style="color:#f87171">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="dark-input @error('end_date') !border-red-500 @enderror"
                           style="color-scheme:dark">
                    @error('end_date')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Times --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="dark-label">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                           class="dark-input" style="color-scheme:dark">
                </div>
                <div>
                    <label class="dark-label">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                           class="dark-input" style="color-scheme:dark">
                </div>
            </div>

            {{-- Capacity + Budget --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="dark-label">Expected Guests <span style="color:#f87171">*</span></label>
                    <input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1" required
                           class="dark-input @error('capacity') !border-red-500 @enderror">
                    @error('capacity')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="dark-label">
                        Total Budget (USD)
                        <span style="color:#6b7280;font-weight:400"> — used for AI suggestions</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:#6b7280">$</span>
                        <input type="number" name="total_budget" value="{{ old('total_budget') }}"
                               min="0" step="100" placeholder="e.g. 5000"
                               class="dark-input" style="padding-left:28px">
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="dark-label">Description</label>
                <textarea name="description" rows="3" placeholder="Brief description of the event..."
                          class="dark-input resize-none">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- ── Venue Preferences ── --}}
        <div class="dark-card space-y-5">
            <div>
                <h3 style="margin-bottom:4px">Venue Preferences</h3>
                <p class="text-xs" style="color:#6b7280">These help AI suggest the right venues in Phnom Penh</p>
            </div>

            {{-- Venue Setting --}}
            <div>
                <label class="dark-label">Venue Setting <span style="color:#f87171">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['indoor' => '🏛️ Indoor', 'outdoor' => '🌿 Outdoor', 'hybrid' => '✨ Hybrid'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="venue_type" value="{{ $val }}" class="sr-only peer"
                               {{ old('venue_type', 'indoor') === $val ? 'checked' : '' }}>
                        <div class="dark-radio-option peer-checked:!border-indigo-500 peer-checked:!text-indigo-300"
                             style="border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px;font-size:13px;font-weight:500;text-align:center;color:#6b7280;cursor:pointer;background:rgba(255,255,255,.03);transition:all .15s">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Event Style --}}
            <div>
                <label class="dark-label">Event Style</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach(['modern' => '💎 Modern', 'traditional' => '🏮 Traditional', 'formal' => '🎩 Formal', 'casual' => '🎉 Casual'] as $val => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="style_pref" value="{{ $val }}" class="sr-only peer"
                               {{ old('style_pref', 'modern') === $val ? 'checked' : '' }}>
                        <div class="peer-checked:!border-indigo-500 peer-checked:!text-indigo-300"
                             style="border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px;font-size:13px;font-weight:500;text-align:center;color:#6b7280;cursor:pointer;background:rgba(255,255,255,.03);transition:all .15s">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Venue + Address --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="dark-label">
                        Venue Name
                        <span style="color:#6b7280;font-weight:400"> — or let AI suggest</span>
                    </label>
                    <input type="text" name="venue" value="{{ old('venue') }}"
                           placeholder="Leave blank for AI recommendations"
                           class="dark-input">
                </div>
                <div>
                    <label class="dark-label">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           placeholder="e.g. Phnom Penh, Cambodia"
                           class="dark-input">
                </div>
            </div>
        </div>

        {{-- ── Event Settings ── --}}
        <div class="dark-card space-y-4">
            <h3>Event Settings</h3>

            @foreach([
                ['name'=>'meal_provided','title'=>'Meal Provided','desc'=>'Include catering in schedule and budget'],
                ['name'=>'is_public','title'=>'Public Event','desc'=>'Anyone can check in via QR code — no invite required'],
            ] as $toggle)
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative flex-shrink-0">
                    <input type="checkbox" name="{{ $toggle['name'] }}" value="1"
                           {{ old($toggle['name']) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-10 h-6 rounded-full transition peer-checked:bg-indigo-600"
                         style="background:rgba(255,255,255,.1)"></div>
                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition
                                peer-checked:translate-x-4"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ $toggle['title'] }}</p>
                    <p class="text-xs" style="color:#6b7280">{{ $toggle['desc'] }}</p>
                </div>
            </label>
            @endforeach
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-3.5 text-white font-semibold rounded-xl flex items-center justify-center gap-2 text-sm"
                style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 14px rgba(79,70,229,.35)"
                onmouseover="this.style.transform='translateY(-1px)'"
                onmouseout="this.style.transform='translateY(0)'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Create Event & Get AI Recommendations
        </button>
        <p class="text-center text-xs" style="color:#4b5563">
            AI will suggest real venues, caterers and decor companies in Phnom Penh based on your inputs.
        </p>
    </form>
</div>
</x-app-layout>
