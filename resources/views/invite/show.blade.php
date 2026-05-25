<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-sm hover:underline mb-2" style="color:#818cf8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-2xl font-bold text-white">Invite Card</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- LEFT: Settings --}}
        <div class="space-y-5">
            {{-- Invite Link --}}
            <div class="rounded-2xl border p-5" style="background:#0d1117;border-color:rgba(99,102,241,.25)">
                <p class="text-sm font-semibold mb-3" style="color:#818cf8">Public Invite Link</p>
                <div class="flex gap-2">
                    <input type="text" value="{{ $inviteUrl }}" readonly onclick="this.select()"
                           class="flex-1 rounded-xl px-3 py-2.5 text-sm text-white focus:outline-none min-w-0"
                           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    <button onclick="copyLink(this)" data-url="{{ $inviteUrl }}"
                            class="px-4 py-2.5 text-white text-sm font-medium rounded-xl flex-shrink-0"
                            style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        Copy
                    </button>
                </div>
                <p class="text-xs mt-2" style="color:#6b7280">Share this link with guests to let them self-register.</p>
            </div>

            {{-- Settings Form --}}
            <div class="rounded-2xl border p-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
                <h3 class="font-semibold text-white mb-4">Invite Card Settings</h3>
                <form method="POST" action="{{ route('events.invite.update', $event) }}" class="space-y-4" id="settings-form">
                    @csrf @method('PATCH')

                    {{-- Template Style --}}
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color:#9ca3af">Template Style</label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['classic'=>'🏛️ Classic','elegant'=>'✨ Elegant','minimal'=>'◻️ Minimal'] as $val=>$label)
                            <label class="cursor-pointer">
                                <input type="radio" name="template_style" value="{{ $val }}"
                                       class="sr-only peer" onchange="updatePreview()"
                                       {{ ($inviteCard?->template_style ?? 'classic')===$val ? 'checked' : '' }}>
                                <div class="border rounded-xl py-2 text-center text-xs font-medium transition text-gray-400 peer-checked:text-indigo-300"
                                     style="border-color:rgba(255,255,255,.1);background:rgba(255,255,255,.04)"
                                     onmouseover="this.style.borderColor='rgba(99,102,241,.4)'"
                                     onmouseout="this.style.borderColor='rgba(255,255,255,.1)'">
                                    {{ $label }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Toggles --}}
                    <div class="space-y-3 pt-1">
                        @foreach(['show_agenda'=>['label'=>'Show Schedule/Agenda','icon'=>'📅'],'show_venue'=>['label'=>'Show Venue & Address','icon'=>'📍'],'show_qr'=>['label'=>'Show QR Code','icon'=>'📱']] as $field=>$info)
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative flex-shrink-0">
                                <input type="checkbox" name="{{ $field }}" value="1" id="{{ $field }}"
                                       onchange="updatePreview()"
                                       {{ $inviteCard?->$field ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-10 h-6 rounded-full transition" style="background:rgba(255,255,255,.1)"
                                     :class="...">
                                    <div class="w-10 h-6 rounded-full peer-checked:bg-indigo-600 transition" style="background:rgba(255,255,255,.1)"></div>
                                </div>
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm" style="color:#9ca3af">{{ $info['icon'] }} {{ $info['label'] }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- Custom Message --}}
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:#9ca3af">Custom Message</label>
                        <textarea name="custom_message" rows="3" id="custom_message"
                                  oninput="updatePreview()"
                                  placeholder="e.g. We warmly invite you to join us for this special occasion..."
                                  class="w-full rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 resize-none"
                                  style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">{{ $inviteCard?->custom_message }}</textarea>
                    </div>

                    @if(session('success'))
                    <div class="p-3 rounded-xl text-sm" style="background:rgba(16,185,129,.1);color:#34d399;border:1px solid rgba(52,211,153,.2)">
                        ✓ {{ session('success') }}
                    </div>
                    @endif

                    <button type="submit"
                            class="w-full py-2.5 text-white font-semibold rounded-xl"
                            style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>

        {{-- RIGHT: Card Preview --}}
        <div class="lg:sticky lg:top-6 self-start">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:#6b7280">Card Preview</p>
                <a href="{{ route('events.invite.guests', $event) }}" class="text-xs hover:underline" style="color:#818cf8">
                    View all guest cards →
                </a>
            </div>
            <div id="invite-preview" class="rounded-2xl overflow-hidden shadow-2xl" style="border:1px solid rgba(255,255,255,.1)">
                @include('invite.partials.card-preview', ['event'=>$event,'inviteCard'=>$inviteCard,'guestName'=>'Guest Name'])
            </div>
            <p class="text-xs text-center mt-3" style="color:#6b7280">
                Preview updates live. Each guest will have their own name and QR code.
            </p>
        </div>
    </div>
</div>

<script>
function copyLink(btn) {
    navigator.clipboard.writeText(btn.dataset.url);
    btn.textContent = 'Copied!';
    setTimeout(()=>btn.textContent='Copy', 2000);
}
function updatePreview() {
    const form = document.getElementById('settings-form');
    const data = new FormData(form);
    data.delete('_method');
    fetch('{{ route('events.invite.preview', $event) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: data
    }).then(r=>r.text()).then(html => {
        document.getElementById('invite-preview').innerHTML = html;
    });
}
</script>
</x-app-layout>
