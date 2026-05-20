<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
        <div>
            <a href="{{ route('events.show', $event) }}" class="text-indigo-600 text-sm hover:underline">&larr; {{ $event->title }}</a>
            <h2 class="text-xl font-semibold text-gray-800 mt-1">Contributions</h2>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-green-600">${{ number_format($stats['total_received'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Received</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-yellow-600">${{ number_format($stats['total_pending'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Pending</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xl font-bold text-gray-900">{{ $stats['count'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Records</p>
            </div>
        </div>

        {{-- Add contribution --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Record Contribution</h3>
            <form method="POST" action="{{ route('events.contributions.store', $event) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Guest *</label>
                    <select name="guest_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        <option value="">Select guest...</option>
                        @foreach($eventGuests as $eg)
                        <option value="{{ $eg->guest_id }}">{{ $eg->guest->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Amount *</label>
                    <input type="number" name="amount" min="0.01" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Method *</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status *</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400" required>
                        <option value="received">Received</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Reference #</label>
                    <input type="text" name="reference_number"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-400">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm rounded-lg transition">
                        Record
                    </button>
                </div>
            </form>
        </div>

        {{-- List --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">Guest</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-left">Method</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contributions as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $c->guest->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-right text-gray-800">${{ number_format($c->amount, 2) }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $c->payment_method }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $c->status === 'received' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $c->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('events.contributions.destroy', [$event, $c]) }}"
                                  onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No contributions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
