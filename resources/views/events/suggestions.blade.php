<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <p class="text-indigo-600 text-sm font-medium mb-1">Step 2 of 2 — AI Suggestions</p>
            <h2 class="text-2xl font-bold text-slate-900">Event Planning Recommendations</h2>
            <p class="text-slate-500 text-sm mt-1">
                Based on your {{ $event->category?->name ?? 'event' }} for
                {{ $event->capacity }} guests in Phnom Penh
            </p>
        </div>
        <form method="POST" action="{{ route('events.suggestions.skip', $event) }}">
            @csrf
            <button type="submit" class="text-sm text-slate-500 hover:text-slate-700 underline">
                Skip suggestions →
            </button>
        </form>
    </div>

    {{-- Budget breakdown --}}
    @if(isset($suggestions['budget_breakdown'], $suggestions['estimated_total']))
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4">Estimated Budget Breakdown</h3>
        <div class="grid grid-cols-5 gap-3 mb-5">
            @foreach($suggestions['budget_breakdown'] as $category => $pct)
            <div class="text-center">
                <div class="bg-slate-100 rounded-full h-2.5 overflow-hidden mb-2">
                    <div class="h-full bg-indigo-500 rounded-full" style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-xs font-medium text-slate-700 capitalize">{{ $category }}</p>
                <p class="text-xs text-indigo-600 font-bold">{{ $pct }}%</p>
            </div>
            @endforeach
        </div>
        <div class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-center">
            <div>
                <p class="text-xs text-slate-500">Total Budget</p>
                <p class="text-xl font-bold text-slate-900">${{ number_format($suggestions['estimated_total']['budget']) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Est. Venue</p>
                <p class="text-xl font-bold text-indigo-600">${{ number_format($suggestions['estimated_total']['venue_cost']) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Est. Catering</p>
                <p class="text-xl font-bold text-emerald-600">${{ number_format($suggestions['estimated_total']['catering_cost']) }}</p>
            </div>
        </div>
        @if(!empty($suggestions['estimated_total']['note']))
        <p class="text-xs text-slate-400 text-center mt-2">{{ $suggestions['estimated_total']['note'] }}</p>
        @endif
    </div>
    @endif

    {{-- Selected venue notice --}}
    <div id="selected-notice" class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm text-emerald-800">
            Venue selected: <strong id="selected-venue-name"></strong> — saved to your event!
        </p>
    </div>

    {{-- Venue recommendations --}}
    <h3 class="font-semibold text-slate-800 mb-4 text-lg">Venue Recommendations</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        @foreach($suggestions['venues'] as $idx => $venue)
        @php
            $imgUrl = $venue['image_url'] ?? '';
            $colors = ['4f46e5','0891b2','059669','d97706','dc2626','7c3aed','0284c7','16a34a'];
            $color  = $colors[$idx % count($colors)];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition venue-card"
             id="venue-card-{{ $idx }}"
             data-venue="{{ addslashes($venue['name']) }}"
             data-address="{{ addslashes($venue['address'] ?? '') }}">

            {{-- Venue image with fallback --}}
            <div class="relative h-44 overflow-hidden bg-slate-800"
                 id="img-container-{{ $idx }}">

                @if($imgUrl)
                <img src="{{ $imgUrl }}"
                     alt="{{ $venue['name'] }}"
                     class="w-full h-full object-cover"
                     onload="this.style.opacity=1"
                     onerror="showVenueFallback({{ $idx }}, '{{ addslashes($venue['name']) }}', '{{ $color }}')"
                     style="opacity:0; transition:opacity 0.3s">
                @endif

                {{-- Fallback shown if image fails or no URL --}}
                <div id="img-fallback-{{ $idx }}"
                     class="{{ $imgUrl ? 'hidden' : '' }} absolute inset-0 flex flex-col items-center justify-center"
                     style="background: linear-gradient(135deg, #{{ $color }}dd, #{{ $color }}88)">
                    <p class="text-white font-bold text-center text-sm px-4 leading-snug drop-shadow">
                        {{ $venue['name'] }}
                    </p>
                    <p class="text-white/70 text-xs mt-1">{{ $venue['area'] }}</p>
                </div>

                {{-- Overlay badges --}}
                <div class="absolute top-3 left-3 z-10">
                    <span class="bg-black/50 backdrop-blur text-white text-xs font-semibold px-2.5 py-1 rounded-full">
                        {{ $venue['price_per_person'] }}/person
                    </span>
                </div>
                @if($idx === 0)
                <div class="absolute top-3 right-3 z-10">
                    <span class="bg-indigo-600 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                        Best Match
                    </span>
                </div>
                @endif
            </div>

            <div class="p-4">
                <h4 class="font-bold text-slate-900 text-sm mb-0.5">{{ $venue['name'] }}</h4>
                <p class="text-xs text-indigo-600 font-medium mb-2">
                    📍 {{ $venue['area'] }}
                </p>
                <p class="text-xs text-slate-500 mb-3 leading-relaxed">{{ $venue['description'] }}</p>

                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded-lg">
                        👥 {{ $venue['capacity_range'] }} pax
                    </span>
                </div>

                @if(!empty($venue['phone']))
                <p class="text-xs text-slate-400 mb-1">📞 {{ $venue['phone'] }}</p>
                @endif
                @if(!empty($venue['address']))
                <p class="text-xs text-slate-400 mb-3">🏠 {{ $venue['address'] }}</p>
                @endif

                <div class="flex gap-2">
                    <button onclick="selectVenue({{ $idx }}, '{{ addslashes($venue['name']) }}', '{{ addslashes($venue['address'] ?? '') }}')"
                            id="select-btn-{{ $idx }}"
                            class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition">
                        Use This Venue
                    </button>
                    @if(!empty($venue['google_maps']))
                    <a href="{{ $venue['google_maps'] }}" target="_blank"
                       class="px-3 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs rounded-xl transition">
                        Map
                    </a>
                    @endif
                    @if(!empty($venue['website']))
                    <a href="{{ $venue['website'] }}" target="_blank"
                       class="px-3 py-2 border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs rounded-xl transition">
                        Web
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Caterers + Decor --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if(!empty($suggestions['caterers']))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 mb-4">🍽️ Catering Recommendations</h3>
            <div class="space-y-4">
                @foreach($suggestions['caterers'] as $cidx => $caterer)
                <div class="border border-slate-100 rounded-xl p-4" id="caterer-card-{{ $cidx }}">
                    <div class="flex items-start justify-between mb-1">
                        <p class="font-semibold text-slate-800 text-sm">{{ $caterer['name'] }}</p>
                        <span class="text-xs text-emerald-600 font-bold ml-2 flex-shrink-0">{{ $caterer['price_range'] }}</span>
                    </div>
                    <p class="text-xs text-indigo-600 mb-1">{{ $caterer['specialty'] }}</p>
                    <p class="text-xs text-slate-500 mb-3">{{ $caterer['description'] }}</p>
                    @if(!empty($caterer['contact']))<p class="text-xs text-slate-400 mb-3">📞 {{ $caterer['contact'] }}</p>@endif
                    <button id="caterer-btn-{{ $cidx }}"
                            onclick="saveContact('caterer', {{ $cidx }}, '{{ addslashes($caterer['name']) }}', '{{ addslashes($caterer['contact'] ?? '') }}', '{{ addslashes($caterer['price_range'] ?? '') }}')"
                            class="w-full py-2 border border-emerald-200 text-emerald-700 hover:bg-emerald-50 text-xs font-medium rounded-xl transition">
                        Save Caterer Info
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($suggestions['decor_companies']))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 mb-4">🌸 Decoration Companies</h3>
            <div class="space-y-4">
                @foreach($suggestions['decor_companies'] as $didx => $decor)
                <div class="border border-slate-100 rounded-xl p-4" id="decor-card-{{ $didx }}">
                    <div class="flex items-start justify-between mb-1">
                        <p class="font-semibold text-slate-800 text-sm">{{ $decor['name'] }}</p>
                        <span class="text-xs text-purple-600 font-bold ml-2 flex-shrink-0">{{ $decor['price_range'] }}</span>
                    </div>
                    <p class="text-xs text-indigo-600 mb-1">{{ $decor['specialty'] }}</p>
                    <p class="text-xs text-slate-500 mb-3">{{ $decor['description'] }}</p>
                    @if(!empty($decor['contact']))<p class="text-xs text-slate-400 mb-3">📞 {{ $decor['contact'] }}</p>@endif
                    <button id="decor-btn-{{ $didx }}"
                            onclick="saveContact('decor', {{ $didx }}, '{{ addslashes($decor['name']) }}', '{{ addslashes($decor['contact'] ?? '') }}', '{{ addslashes($decor['price_range'] ?? '') }}')"
                            class="w-full py-2 border border-purple-200 text-purple-700 hover:bg-purple-50 text-xs font-medium rounded-xl transition">
                        Save Decor Info
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Planning tips --}}
    @if(!empty($suggestions['planning_tips']))
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 mb-8">
        <h3 class="font-semibold text-indigo-900 mb-3">💡 Planning Tips for Phnom Penh</h3>
        <ul class="space-y-2">
            @foreach($suggestions['planning_tips'] as $tip)
            <li class="flex items-start gap-2 text-sm text-indigo-800">
                <svg class="w-4 h-4 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $tip }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('events.show', $event) }}"
           class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl transition">
            Continue to Event Dashboard →
        </a>
    </div>
</div>

<script>
function showVenueFallback(idx, name, color) {
    const img = document.querySelector('#img-container-' + idx + ' img');
    if (img) img.style.display = 'none';
    const fallback = document.getElementById('img-fallback-' + idx);
    if (fallback) fallback.classList.remove('hidden');
}

// Trigger load animation for images
document.querySelectorAll('.venue-card img').forEach(img => {
    if (img.complete && img.naturalWidth > 0) img.style.opacity = 1;
});

function saveContact(type, idx, name, contact, price) {
    const btn = document.getElementById(type + '-btn-' + idx);
    const card = document.getElementById(type + '-card-' + idx);

    // Copy to clipboard
    const text = name + '\n' + contact + '\n' + price;
    navigator.clipboard.writeText(text).catch(() => {});

    // Mark as saved
    btn.textContent = '✓ Saved';
    btn.disabled    = true;
    btn.className   = 'w-full py-2 bg-emerald-500 text-white text-xs font-medium rounded-xl';
    card.classList.add('ring-2', type === 'caterer' ? 'ring-emerald-300' : 'ring-purple-300');

    // Show global saved notice
    const notice = document.getElementById('selected-notice');
    document.getElementById('selected-venue-name').textContent = name + ' (' + (type === 'caterer' ? 'Caterer' : 'Decor') + ')';
    notice.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selectVenue(idx, name, address) {
    const btn = document.getElementById('select-btn-' + idx);
    btn.textContent = 'Saving...';
    btn.disabled    = true;

    fetch('{{ route('events.suggestions.select', $event) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ venue: name, address: address })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.venue-card').forEach((c, i) => {
                c.classList.remove('ring-2', 'ring-indigo-500');
                const b = document.getElementById('select-btn-' + i);
                if (b && i !== idx) {
                    b.textContent = 'Use This Venue';
                    b.disabled    = false;
                    b.className   = 'flex-1 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition';
                }
            });
            document.getElementById('venue-card-' + idx).classList.add('ring-2', 'ring-indigo-500');
            btn.textContent = '✓ Selected';
            btn.className   = 'flex-1 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-xl';

            document.getElementById('selected-venue-name').textContent = name;
            document.getElementById('selected-notice').classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    })
    .catch(() => {
        btn.textContent = 'Use This Venue';
        btn.disabled    = false;
    });
}
</script>
</x-app-layout>
