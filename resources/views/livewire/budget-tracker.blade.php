<div class="space-y-4">
@if($summary['has_budget'])

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500 mb-1">Total Budget</p>
            <p class="text-xl font-bold text-slate-900">${{ number_format($summary['total_budget'], 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500 mb-1">Allocated</p>
            <p class="text-xl font-bold text-indigo-600">${{ number_format($summary['total_allocated'], 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500 mb-1">Spent So Far</p>
            <p class="text-xl font-bold {{ $summary['over_budget'] ? 'text-red-600' : 'text-emerald-600' }}">
                ${{ number_format($summary['total_actual'], 0) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500 mb-1">Remaining</p>
            <p class="text-xl font-bold {{ $summary['unallocated'] < 0 ? 'text-red-600' : 'text-slate-700' }}">
                {{ $summary['unallocated'] < 0 ? '-' : '' }}${{ number_format(abs($summary['unallocated']), 0) }}
            </p>
        </div>
    </div>

    {{-- Overall progress bar --}}
    @php
        $spentPct = $summary['total_budget'] > 0
            ? min(100, round(($summary['total_actual'] / $summary['total_budget']) * 100))
            : 0;
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200 px-5 py-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-medium text-slate-700">Overall Spending</p>
            <p class="text-sm font-bold {{ $spentPct >= 90 ? 'text-red-600' : ($spentPct >= 70 ? 'text-amber-600' : 'text-emerald-600') }}">
                {{ $spentPct }}% used
            </p>
        </div>
        <div class="bg-slate-100 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full transition-all
                        {{ $spentPct >= 90 ? 'bg-red-500' : ($spentPct >= 70 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                 style="width: {{ $spentPct }}%"></div>
        </div>
        <div class="flex justify-between mt-1 text-xs text-slate-400">
            <span>$0</span>
            <span>${{ number_format($summary['total_budget'], 0) }}</span>
        </div>
    </div>

    {{-- Line items --}}
    @if($budget && $budget->items->count())
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- Header row --}}
        <div class="hidden lg:grid grid-cols-12 gap-2 px-5 py-3 bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wide">
            <div class="col-span-5">Item</div>
            <div class="col-span-2 text-right">Allocated</div>
            <div class="col-span-2 text-right">Spent</div>
            <div class="col-span-2 text-right">Left</div>
            <div class="col-span-1"></div>
        </div>

        @foreach($budget->items as $item)
        @php
            $alloc     = (float)$item->allocated_amount;
            $actual    = (float)$item->actual_amount;
            $left      = $alloc - $actual;
            $isOver    = $actual > $alloc;
            $itemPct   = $alloc > 0 ? min(100, round(($actual / $alloc) * 100)) : 0;
            $barColor  = $itemPct >= 100 ? 'bg-red-500' : ($itemPct >= 80 ? 'bg-amber-400' : 'bg-indigo-400');
        @endphp
        <div class="px-5 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition">
            <div class="lg:grid lg:grid-cols-12 lg:gap-2 lg:items-center space-y-2 lg:space-y-0">

                {{-- Name + bar --}}
                <div class="lg:col-span-5 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ $item->line_item }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full rounded-full {{ $barColor }}" style="width:{{ $itemPct }}%"></div>
                        </div>
                        <span class="text-xs text-slate-400 flex-shrink-0">{{ $itemPct }}%</span>
                    </div>
                </div>

                {{-- Allocated --}}
                <div class="lg:col-span-2 flex lg:block justify-between">
                    <span class="lg:hidden text-xs text-slate-500">Allocated</span>
                    <p class="text-sm font-medium text-slate-700 lg:text-right">${{ number_format($alloc, 0) }}</p>
                </div>

                {{-- Spent --}}
                <div class="lg:col-span-2 flex lg:block justify-between">
                    <span class="lg:hidden text-xs text-slate-500">Spent</span>
                    <p class="text-sm font-bold lg:text-right {{ $isOver ? 'text-red-600' : 'text-emerald-600' }}">
                        ${{ number_format($actual, 0) }}
                        @if($isOver)<span class="text-xs ml-1">over!</span>@endif
                    </p>
                </div>

                {{-- Remaining --}}
                <div class="lg:col-span-2 flex lg:block justify-between">
                    <span class="lg:hidden text-xs text-slate-500">Left</span>
                    <p class="text-sm font-medium lg:text-right {{ $left < 0 ? 'text-red-600' : 'text-slate-600' }}">
                        {{ $left < 0 ? '-' : '' }}${{ number_format(abs($left), 0) }}
                    </p>
                </div>

                {{-- Input to update actual spend --}}
                <div class="lg:col-span-1 flex items-center gap-1">
                    <div class="relative flex-1">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-slate-400">$</span>
                        <input type="number"
                               value="{{ $actual }}"
                               min="0"
                               step="1"
                               wire:change="updateActual({{ $item->id }}, $event.target.value)"
                               title="Update actual spend"
                               class="w-full pl-5 pr-1 py-1.5 border border-slate-300 rounded-lg text-xs text-right
                                      focus:outline-none focus:border-indigo-400
                                      {{ $isOver ? 'border-red-300 bg-red-50' : '' }}">
                    </div>
                </div>

            </div>
        </div>
        @endforeach

        {{-- Footer totals --}}
        <div class="hidden lg:grid grid-cols-12 gap-2 px-5 py-3 bg-slate-50 border-t border-slate-200 text-sm font-semibold">
            <div class="col-span-5 text-slate-700">Total</div>
            <div class="col-span-2 text-right text-slate-700">${{ number_format($summary['total_allocated'], 0) }}</div>
            <div class="col-span-2 text-right {{ $summary['over_budget'] ? 'text-red-600' : 'text-emerald-600' }}">
                ${{ number_format($summary['total_actual'], 0) }}
            </div>
            <div class="col-span-2 text-right {{ $summary['unallocated'] < 0 ? 'text-red-600' : 'text-slate-700' }}">
                {{ $summary['unallocated'] < 0 ? '-' : '' }}${{ number_format(abs($summary['unallocated']), 0) }}
            </div>
            <div class="col-span-1"></div>
        </div>
    </div>
    @endif

@else
    <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
        <p class="text-slate-400 text-sm">No budget set yet. Set a total budget above to get started.</p>
    </div>
@endif
</div>
