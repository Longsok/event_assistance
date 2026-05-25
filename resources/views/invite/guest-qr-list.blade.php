<x-app-layout>
<div class="py-6 px-4 sm:px-6 max-w-5xl mx-auto">
    <div class="flex items-start justify-between mb-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-sm hover:underline" style="color:#818cf8">← Back to Event</a>
            <h2 class="text-2xl font-bold text-white mt-1">Guest Invite Cards</h2>
            <p class="text-sm mt-0.5" style="color:#6b7280">{{ $eventGuests->count() }} guests — each has a unique QR code</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('events.invite.show', $event) }}"
               class="px-4 py-2 text-sm font-medium rounded-xl transition"
               style="background:rgba(255,255,255,.07);color:#9ca3af;border:1px solid rgba(255,255,255,.1)">
                Invite Card Settings
            </a>
            <button onclick="window.print()"
                    class="px-4 py-2 text-white text-sm font-medium rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                Print All Cards
            </button>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-5">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#6b7280" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="guest-search" placeholder="Search guest by name..."
                   class="w-full sm:w-80 pl-9 pr-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                   style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
        </div>
    </div>

    {{-- Guest cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="guests-grid">
        @forelse($eventGuests as $eg)
        <div class="guest-card rounded-2xl overflow-hidden shadow-lg print:break-inside-avoid"
            style="border:1px solid rgba(255,255,255,.1)"
            data-name="{{ strtolower($eg->guest->name) }}">

            @include('invite.partials.card-preview', [
                'event'      => $event,
                'inviteCard' => $event->inviteCard,
                'guestName'  => $eg->guest->name,
                'guestCode'  => $eg->guest_code,
            ])

            <div class="flex items-center justify-between px-4 py-3"
                style="background:#0d1117;border-top:1px solid rgba(255,255,255,.07)">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium"
                    style="{{ $eg->rsvp_status==='confirmed' ? 'background:rgba(16,185,129,.12);color:#34d399' : 'background:rgba(251,191,36,.1);color:#fbbf24' }}">
                    {{ ucfirst($eg->rsvp_status) }}
                </span>
                <span class="text-xs font-mono" style="color:#6b7280">{{ $eg->guest_code }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-3 rounded-2xl border py-12 text-center text-sm" style="background:#0d1117;border-color:rgba(255,255,255,.07);color:#6b7280">
            No guests added yet.
            <a href="{{ route('events.show', $event) }}?tab=guests" class="hover:underline ml-1" style="color:#818cf8">Add guests</a>
        </div>
        @endforelse
    </div>
</div>

<script>
document.getElementById('guest-search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.guest-card').forEach(c => {
        c.style.display = c.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>

<style>
@media print {
    header, nav, footer, #guest-search, button { display: none !important; }
    .guest-card { page-break-inside: avoid; background: white !important; border: 1px solid #e2e8f0 !important; }
    .guest-card * { color: black !important; }
}
</style>
</x-app-layout>
