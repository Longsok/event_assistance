<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium mb-1" style="color:#818cf8">Step 2 of 2 — AI Suggestions</p>
            <h2 class="text-2xl font-bold" style="color:var(--text-strong)">Event Planning Recommendations</h2>
            <p class="text-sm mt-1" style="color:var(--text-soft)">
                Based on your {{ $event->category?->name ?? 'event' }} for
                {{ $event->capacity }} guests in Phnom Penh
            </p>
        </div>
        <form method="POST" action="{{ route('events.suggestions.skip', $event) }}">
            @csrf
            <button type="submit" class="text-sm underline" style="color:var(--text-soft)"
                    onmouseover="this.style.color='var(--text-strong)'" onmouseout="this.style.color='var(--text-soft)'">
                Skip suggestions →
            </button>
        </form>
    </div>

    {{-- Budget breakdown --}}
    @if(isset($suggestions['budget_breakdown'], $suggestions['estimated_total']))
    <div class="rounded-2xl border shadow-sm p-6 mb-6" style="background:var(--panel);border-color:var(--border)">
        <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Estimated Budget Breakdown</h3>
        <div class="grid grid-cols-5 gap-3 mb-5">
            @foreach($suggestions['budget_breakdown'] as $category => $pct)
            <div class="text-center">
                <div class="rounded-full h-2.5 overflow-hidden mb-2" style="background:var(--input-bg)">
                    <div class="h-full bg-indigo-500 rounded-full" style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-xs font-medium capitalize" style="color:var(--text)">{{ $category }}</p>
                <p class="text-xs font-bold" style="color:#818cf8">{{ $pct }}%</p>
            </div>
            @endforeach
        </div>
        <div class="grid grid-cols-3 gap-4 pt-4 text-center" style="border-top:1px solid var(--border)">
            <div>
                <p class="text-xs" style="color:var(--text-soft)">Total Budget</p>
                <p class="text-xl font-bold" style="color:var(--text-strong)">${{ number_format($suggestions['estimated_total']['budget']) }}</p>
            </div>
            <div>
                <p class="text-xs" style="color:var(--text-soft)">Est. Venue</p>
                <p class="text-xl font-bold" style="color:#818cf8">${{ number_format($suggestions['estimated_total']['venue_cost']) }}</p>
            </div>
            <div>
                <p class="text-xs" style="color:var(--text-soft)">Est. Catering</p>
                <p class="text-xl font-bold" style="color:#34d399">${{ number_format($suggestions['estimated_total']['catering_cost']) }}</p>
            </div>
        </div>
        @if(!empty($suggestions['estimated_total']['note']))
        <p class="text-xs text-center mt-2" style="color:var(--text-soft)">{{ $suggestions['estimated_total']['note'] }}</p>
        @endif
    </div>
    @endif

    {{-- Selected venue notice --}}
    <div id="selected-notice" class="hidden rounded-2xl p-4 mb-6 flex items-center gap-3"
         style="background:rgba(16,185,129,.1);border:1px solid rgba(52,211,153,.3)">
        <svg class="w-5 h-5 flex-shrink-0" style="color:#34d399" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm" style="color:#34d399">
            Venue selected: <strong id="selected-venue-name"></strong> — saved to your event!
        </p>
    </div>

    {{-- Venue recommendations --}}
    <h3 class="font-semibold mb-4 text-lg" style="color:var(--text-strong)">Venue Recommendations</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        @foreach($suggestions['venues'] as $idx => $venue)
        @php
            $imgUrl = $venue['image_url'] ?? '';
            $colors = ['4f46e5','0891b2','059669','d97706','dc2626','7c3aed','0284c7','16a34a'];
            $color  = $colors[$idx % count($colors)];
        @endphp
        <div class="rounded-2xl border shadow-sm overflow-hidden hover:shadow-md transition venue-card"
             style="background:var(--panel);border-color:var(--border)"
             id="venue-card-{{ $idx }}"
             data-venue="{{ addslashes($venue['name']) }}"
             data-address="{{ addslashes($venue['address'] ?? '') }}">

            {{-- Venue image with fallback --}}
            <div class="relative h-44 overflow-hidden" style="background:#1f2937"
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
                <h4 class="font-bold text-sm mb-0.5" style="color:var(--text-strong)">{{ $venue['name'] }}</h4>
                <p class="text-xs font-medium mb-2" style="color:#818cf8">
                    📍 {{ $venue['area'] }}
                </p>
                <p class="text-xs mb-3 leading-relaxed" style="color:var(--text-soft)">{{ $venue['description'] }}</p>

                <div class="flex flex-wrap gap-1.5 mb-3">
                    <span class="text-xs px-2 py-1 rounded-lg" style="background:var(--input-bg);color:var(--text-soft)">
                        👥 {{ $venue['capacity_range'] }} pax
                    </span>
                </div>

                @if(!empty($venue['phone']))
                <p class="text-xs mb-1" style="color:var(--text-soft)">📞 {{ $venue['phone'] }}</p>
                @endif
                @if(!empty($venue['address']))
                <p class="text-xs mb-3" style="color:var(--text-soft)">🏠 {{ $venue['address'] }}</p>
                @endif

                <div class="flex gap-2">
                    <button onclick="selectVenue({{ $idx }}, '{{ addslashes($venue['name']) }}', '{{ addslashes($venue['address'] ?? '') }}')"
                            id="select-btn-{{ $idx }}"
                            class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition">
                        Use This Venue
                    </button>
                    @if(!empty($venue['google_maps']))
                    <a href="{{ $venue['google_maps'] }}" target="_blank"
                       class="px-3 py-2 border text-xs rounded-xl transition"
                       style="border-color:var(--input-border);color:var(--text-soft)"
                       onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                        Map
                    </a>
                    @endif
                    @if(!empty($venue['website']))
                    <a href="{{ $venue['website'] }}" target="_blank"
                       class="px-3 py-2 border text-xs rounded-xl transition"
                       style="border-color:var(--input-border);color:var(--text-soft)"
                       onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
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
        <div class="rounded-2xl border shadow-sm p-5" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">🍽️ Catering Recommendations</h3>
            <div class="space-y-4">
                @foreach($suggestions['caterers'] as $cidx => $caterer)
                <div class="border rounded-xl p-4" style="border-color:var(--border)" id="caterer-card-{{ $cidx }}">
                    <div class="flex items-start justify-between mb-1">
                        <p class="font-semibold text-sm" style="color:var(--text-strong)">{{ $caterer['name'] }}</p>
                        <span class="text-xs font-bold ml-2 flex-shrink-0" style="color:#34d399">{{ $caterer['price_range'] }}</span>
                    </div>
                    <p class="text-xs mb-1" style="color:#818cf8">{{ $caterer['specialty'] }}</p>
                    <p class="text-xs mb-3" style="color:var(--text-soft)">{{ $caterer['description'] }}</p>
                    @if(!empty($caterer['contact']))<p class="text-xs mb-3" style="color:var(--text-soft)">📞 {{ $caterer['contact'] }}</p>@endif
                    <button id="caterer-btn-{{ $cidx }}"
                            onclick="saveContact('caterer', {{ $cidx }}, '{{ addslashes($caterer['name']) }}', '{{ addslashes($caterer['contact'] ?? '') }}', '{{ addslashes($caterer['price_range'] ?? '') }}')"
                            class="w-full py-2 border text-xs font-medium rounded-xl transition"
                            style="border-color:rgba(52,211,153,.3);color:#34d399"
                            onmouseover="this.style.background='rgba(16,185,129,.1)'" onmouseout="this.style.background='transparent'">
                        Save Caterer Info
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($suggestions['decor_companies']))
        <div class="rounded-2xl border shadow-sm p-5" style="background:var(--panel);border-color:var(--border)">
            <h3 class="font-semibold mb-4" style="color:var(--text-strong)">🌸 Decoration Companies</h3>
            <div class="space-y-4">
                @foreach($suggestions['decor_companies'] as $didx => $decor)
                <div class="border rounded-xl p-4" style="border-color:var(--border)" id="decor-card-{{ $didx }}">
                    <div class="flex items-start justify-between mb-1">
                        <p class="font-semibold text-sm" style="color:var(--text-strong)">{{ $decor['name'] }}</p>
                        <span class="text-xs font-bold ml-2 flex-shrink-0" style="color:#c084fc">{{ $decor['price_range'] }}</span>
                    </div>
                    <p class="text-xs mb-1" style="color:#818cf8">{{ $decor['specialty'] }}</p>
                    <p class="text-xs mb-3" style="color:var(--text-soft)">{{ $decor['description'] }}</p>
                    @if(!empty($decor['contact']))<p class="text-xs mb-3" style="color:var(--text-soft)">📞 {{ $decor['contact'] }}</p>@endif
                    <button id="decor-btn-{{ $didx }}"
                            onclick="saveContact('decor', {{ $didx }}, '{{ addslashes($decor['name']) }}', '{{ addslashes($decor['contact'] ?? '') }}', '{{ addslashes($decor['price_range'] ?? '') }}')"
                            class="w-full py-2 border text-xs font-medium rounded-xl transition"
                            style="border-color:rgba(192,132,252,.3);color:#c084fc"
                            onmouseover="this.style.background='rgba(168,85,247,.1)'" onmouseout="this.style.background='transparent'">
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
    <div class="rounded-2xl p-5 mb-8" style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2)">
        <h3 class="font-semibold mb-3" style="color:#a5b4fc">💡 Planning Tips for Phnom Penh</h3>
        <ul class="space-y-2">
            @foreach($suggestions['planning_tips'] as $tip)
            <li class="flex items-start gap-2 text-sm" style="color:var(--text)">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

document.querySelectorAll('.venue-card img').forEach(img => {
    if (img.complete && img.naturalWidth > 0) img.style.opacity = 1;
});

function saveContact(type, idx, name, contact, price) {
    const btn = document.getElementById(type + '-btn-' + idx);
    const card = document.getElementById(type + '-card-' + idx);

    const text = name + '\n' + contact + '\n' + price;
    navigator.clipboard.writeText(text).catch(() => {});

    btn.textContent = '✓ Saved';
    btn.disabled    = true;
    btn.className   = 'w-full py-2 bg-emerald-500 text-white text-xs font-medium rounded-xl';
    btn.style.cssText = '';
    card.classList.add('ring-2', type === 'caterer' ? 'ring-emerald-300' : 'ring-purple-300');

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