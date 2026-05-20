<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Invite Card</h2>
        </div>

        {{-- Invite Link --}}
        <div class="bg-indigo-50 rounded-xl border border-indigo-100 p-5">
            <p class="text-sm font-medium text-indigo-800 mb-2">Public Invite Link</p>
            <div class="flex gap-2">
                <input type="text" value="{{ $inviteUrl }}" readonly
                       class="flex-1 bg-white border border-indigo-200 rounded-lg px-3 py-2 text-sm text-gray-700"
                       onclick="this.select()">
                <button onclick="navigator.clipboard.writeText('{{ $inviteUrl }}');alert('Copied!')"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                    Copy
                </button>
            </div>
            <p class="text-xs text-indigo-600 mt-2">Share this link with guests to let them self-register.</p>
        </div>

        {{-- Settings --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Invite Card Settings</h3>
            <form method="POST" action="{{ route('events.invite.update', $event) }}" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Style</label>
                    <select name="template_style" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                        @foreach(['default','elegant','minimal'] as $style)
                        <option value="{{ $style }}" {{ ($inviteCard?->template_style ?? 'default') === $style ? 'selected' : '' }}>
                            {{ ucfirst($style) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    @foreach(['show_agenda'=>'Show Agenda','show_venue'=>'Show Venue','show_qr'=>'Show QR Code'] as $field => $label)
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1"
                               {{ $inviteCard?->$field ? 'checked' : '' }}>
                        <label for="{{ $field }}" class="text-sm text-gray-700">{{ $label }}</label>
                    </div>
                    @endforeach
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Message</label>
                    <textarea name="custom_message" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">{{ $inviteCard?->custom_message }}</textarea>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Save Settings
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
