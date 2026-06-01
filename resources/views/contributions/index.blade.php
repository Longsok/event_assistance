<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-sm hover:underline" style="color:#818cf8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-xl font-semibold mt-1" style="color:var(--text-strong)">Contributions</h2>
    </div>
    <div class="grid grid-cols-3 gap-4">
        @foreach([['label'=>'Received','value'=>'$'.number_format($stats['total_received'],2),'color'=>'#34d399'],['label'=>'Pending','value'=>'$'.number_format($stats['total_pending'],2),'color'=>'#fbbf24'],['label'=>'Total Records','value'=>$stats['count'],'color'=>'var(--text-strong)']] as $s)
        <div class="rounded-2xl border shadow-sm p-4 text-center" style="background:var(--panel);border-color:var(--border)">
            <p class="text-xl font-bold" style="color:{{ $s['color'] }}">{{ $s['value'] }}</p>
            <p class="text-xs mt-1" style="color:var(--text-soft)">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>
    <div class="rounded-2xl border shadow-sm p-5" style="background:var(--panel);border-color:var(--border)">
        <h3 class="font-semibold mb-4" style="color:var(--text-strong)">Record Contribution</h3>
        <form method="POST" action="{{ route('events.contributions.store', $event) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Guest *</label>
                <select name="guest_id" required class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    <option value="" style="background:var(--panel)">Select guest...</option>
                    @foreach($eventGuests as $eg)
                    <option value="{{ $eg->guest_id }}" style="background:var(--panel)">{{ $eg->guest->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Amount *</label>
                <input type="number" name="amount" min="0.01" step="0.01" required
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Method *</label>
                <select name="payment_method" required class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    <option value="cash" style="background:var(--panel)">Cash</option>
                    <option value="bank_transfer" style="background:var(--panel)">Bank Transfer</option>
                    <option value="other" style="background:var(--panel)">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Status *</label>
                <select name="status" required class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    <option value="received" style="background:var(--panel)">Received</option>
                    <option value="pending" style="background:var(--panel)">Pending</option>
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Reference #</label>
                <input type="text" name="reference_number" class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Record</button>
            </div>
        </form>
    </div>
    <div class="rounded-2xl border shadow-sm overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <table class="w-full text-sm">
            <thead class="uppercase text-xs" style="background:var(--panel-input);color:var(--text-soft);border-bottom:1px solid var(--border)">
                <tr>
                    <th class="px-5 py-3 text-left">Guest</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3 text-left">Method</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $c)
                <tr style="border-bottom:1px solid var(--border)"
                    onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                    <td class="px-5 py-3 font-medium" style="color:var(--text-strong)">{{ $c->guest->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-right" style="color:var(--text-strong)">${{ number_format($c->amount, 2) }}</td>
                    <td class="px-5 py-3" style="color:var(--text-soft)">{{ $c->payment_method }}</td>
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
                <tr><td colspan="5" class="px-5 py-8 text-center" style="color:var(--text-soft)">No contributions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>