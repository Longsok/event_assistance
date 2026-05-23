<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-xl font-semibold text-slate-900 mt-1">Contributions</h2>
    </div>
    <div class="grid grid-cols-3 gap-4">
        @foreach([['label'=>'Received','value'=>'$'.number_format($stats['total_received'],2),'color'=>'text-emerald-600'],['label'=>'Pending','value'=>'$'.number_format($stats['total_pending'],2),'color'=>'text-amber-600'],['label'=>'Total Records','value'=>$stats['count'],'color'=>'text-slate-900']] as $s)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Record Contribution</h3>
        <form method="POST" action="{{ route('events.contributions.store', $event) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs text-slate-500 mb-1">Guest *</label>
                <select name="guest_id" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                    <option value="">Select guest...</option>
                    @foreach($eventGuests as $eg)
                    <option value="{{ $eg->guest_id }}">{{ $eg->guest->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Amount *</label>
                <input type="number" name="amount" min="0.01" step="0.01" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Method *</label>
                <select name="payment_method" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Status *</label>
                <select name="status" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                    <option value="received">Received</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Reference #</label>
                <input type="text" name="reference_number" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Record</button>
            </div>
        </form>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-left">Guest</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3 text-left">Method</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contributions as $c)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $c->guest->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-right text-slate-800">${{ number_format($c->amount, 2) }}</td>
                    <td class="px-5 py-3 text-slate-500">{{ $c->payment_method }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs {{ $c->status==='received' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $c->status }}</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('events.contributions.destroy', [$event, $c]) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600 font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">No contributions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
