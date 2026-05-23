<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-xl font-semibold text-slate-900 mt-1">Invite Card</h2>
    </div>
    <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-5">
        <p class="text-sm font-medium text-indigo-800 mb-2">Public Invite Link</p>
        <div class="flex gap-2">
            <input type="text" value="{{ $inviteUrl }}" readonly onclick="this.select()"
                   class="flex-1 bg-white border border-indigo-200 rounded-xl px-3 py-2.5 text-sm text-slate-700">
            <button onclick="navigator.clipboard.writeText('{{ $inviteUrl }}');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Copy</button>
        </div>
        <p class="text-xs text-indigo-600 mt-2">Share this link with guests to let them self-register.</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Invite Card Settings</h3>
        <form method="POST" action="{{ route('events.invite.update', $event) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Template Style</label>
                <select name="template_style" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                    @foreach(['classic','elegant','minimal'] as $style)
                    <option value="{{ $style }}" {{ ($inviteCard?->template_style ?? 'classic')===$style ? 'selected' : '' }}>{{ ucfirst($style) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-3">
                @foreach(['show_agenda'=>'Show Agenda','show_venue'=>'Show Venue','show_qr'=>'Show QR Code'] as $field => $label)
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1" {{ $inviteCard?->$field ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="{{ $field }}" class="text-sm text-slate-700">{{ $label }}</label>
                </div>
                @endforeach
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Custom Message</label>
                <textarea name="custom_message" rows="3"
                          class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">{{ $inviteCard?->custom_message }}</textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-medium transition">Save Settings</button>
        </form>
    </div>
</div>
</x-app-layout>
