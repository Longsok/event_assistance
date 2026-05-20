<div class="space-y-4">
    @if($summary['has_budget'])
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex flex-wrap gap-4 mb-4">
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Total Budget</p>
                <p class="text-xl font-bold text-gray-900">${{ number_format($summary['total_budget'], 2) }}</p>
            </div>
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Allocated</p>
                <p class="text-xl font-bold text-indigo-600">${{ number_format($summary['total_allocated'], 2) }}</p>
            </div>
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Actual Spent</p>
                <p class="text-xl font-bold {{ $summary['over_budget'] ? 'text-red-600' : 'text-green-600' }}">
                    ${{ number_format($summary['total_actual'], 2) }}
                </p>
            </div>
            <div class="flex-1 min-w-32">
                <p class="text-xs text-gray-500">Remaining</p>
                <p class="text-xl font-bold {{ $summary['unallocated'] < 0 ? 'text-red-600' : 'text-gray-700' }}">
                    ${{ number_format($summary['unallocated'], 2) }}
                </p>
            </div>
        </div>
        @php $spentPct = $summary['total_budget'] > 0 ? min(100, round(($summary['total_actual'] / $summary['total_budget']) * 100)) : 0; @endphp
        <div class="bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="h-full rounded-full transition-all {{ $summary['over_budget'] ? 'bg-red-500' : 'bg-green-500' }}"
                 style="width: {{ $spentPct }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1 text-right">{{ $spentPct }}% spent</p>
    </div>
    @if($budget && $budget->items->count())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @foreach($budget->items as $item)
        <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $item->line_item }}</p>
                <p class="text-xs text-gray-400">Allocated: ${{ number_format($item->allocated_amount, 2) }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Actual $</span>
                <input type="number" value="{{ $item->actual_amount }}" min="0" step="0.01"
                       wire:change="updateActual({{ $item->id }}, $event.target.value)"
                       class="w-24 border border-gray-300 rounded-lg px-2 py-1.5 text-sm text-right focus:outline-none focus:border-indigo-400">
            </div>
            <span class="text-xs {{ $item->isOverBudget() ? 'text-red-500' : 'text-green-600' }} flex-shrink-0">
                ${{ number_format($item->remaining, 2) }} left
            </span>
        </div>
        @endforeach
    </div>
    @endif
    @else
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 text-center text-gray-400">
        <p>No budget set yet.</p>
    </div>
    @endif
</div>
