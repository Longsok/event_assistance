<div class="space-y-4">
@if($summary['has_budget'])

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-2xl border p-4" style="background:var(--panel);border-color:var(--border)">
            <p class="text-xs mb-1" style="color:var(--text-soft)">Total Budget</p>
            <p class="text-xl font-bold" style="color:var(--text-strong)">${{ number_format($summary['total_budget'], 0) }}</p>
        </div>
        <div class="rounded-2xl border p-4" style="background:var(--panel);border-color:rgba(99,102,241,.2)">
            <p class="text-xs mb-1" style="color:var(--text-soft)">Allocated</p>
            <p class="text-xl font-bold" style="color:#818cf8">${{ number_format($summary['total_allocated'], 0) }}</p>
        </div>
        <div class="rounded-2xl border p-4" style="background:var(--panel);border-color:{{ $summary['over_budget'] ? 'rgba(239,68,68,.3)' : 'rgba(52,211,153,.2)' }}">
            <p class="text-xs mb-1" style="color:var(--text-soft)">Spent So Far</p>
            <p class="text-xl font-bold" style="color:{{ $summary['over_budget'] ? '#f87171' : '#34d399' }}">
                ${{ number_format($summary['total_actual'], 0) }}
            </p>
        </div>
        <div class="rounded-2xl border p-4" style="background:var(--panel);border-color:var(--border)">
            <p class="text-xs mb-1" style="color:var(--text-soft)">Remaining</p>
            <p class="text-xl font-bold" style="color:{{ $summary['unallocated'] < 0 ? '#f87171' : 'var(--text-strong)' }}">
                {{ $summary['unallocated'] < 0 ? '-' : '' }}${{ number_format(abs($summary['unallocated']), 0) }}
            </p>
        </div>
    </div>

    {{-- Overall progress --}}
    @php
        $spentPct = $summary['total_budget'] > 0
            ? min(100, round(($summary['total_actual'] / $summary['total_budget']) * 100))
            : 0;
    @endphp
    <div class="rounded-2xl border px-5 py-4" style="background:var(--panel);border-color:var(--border)">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-medium" style="color:var(--text-strong)">Overall Spending</p>
            <p class="text-sm font-bold" style="color:{{ $spentPct >= 90 ? '#f87171' : ($spentPct >= 70 ? '#fbbf24' : '#34d399') }}">
                {{ $spentPct }}% used
            </p>
        </div>
        <div class="rounded-full h-3 overflow-hidden" style="background:var(--input-bg)">
            <div class="h-full rounded-full transition-all"
                 style="width:{{ $spentPct }}%;background:{{ $spentPct >= 90 ? '#ef4444' : ($spentPct >= 70 ? '#f59e0b' : '#10b981') }}"></div>
        </div>
        <div class="flex justify-between mt-1 text-xs" style="color:var(--text-soft)">
            <span>$0</span>
            <span>${{ number_format($summary['total_budget'], 0) }}</span>
        </div>
    </div>

    {{-- Line items --}}
    @if($budget && $budget->items->count())
    <div class="rounded-2xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
        <div class="hidden lg:grid grid-cols-12 gap-2 px-5 py-3 text-xs font-semibold uppercase tracking-wide"
             style="background:var(--hover);border-bottom:1px solid var(--border);color:var(--text-soft)">
            <div class="col-span-5">Item</div>
            <div class="col-span-2 text-right">Allocated</div>
            <div class="col-span-2 text-right">Spent</div>
            <div class="col-span-2 text-right">Left</div>
            <div class="col-span-1"></div>
        </div>

        @foreach($budget->items as $item)
        @php
            $alloc    = (float)$item->allocated_amount;
            $actual   = (float)$item->actual_amount;
            $left     = $alloc - $actual;
            $isOver   = $actual > $alloc;
            $itemPct  = $alloc > 0 ? min(100, round(($actual / $alloc) * 100)) : 0;
            $barColor = $itemPct >= 100 ? '#ef4444' : ($itemPct >= 80 ? '#f59e0b' : '#818cf8');
        @endphp
        <div class="px-5 py-4 transition" style="border-bottom:1px solid var(--border-soft)"
             onmouseover="this.style.background='var(--hover)'"
             onmouseout="this.style.background='transparent'">
            <div class="lg:grid lg:grid-cols-12 lg:gap-2 lg:items-center space-y-2 lg:space-y-0">

                <div class="lg:col-span-5 min-w-0">
                    <p class="text-sm font-medium truncate" style="color:var(--text-strong)">{{ $item->line_item }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="flex-1 rounded-full h-1.5 overflow-hidden" style="background:var(--input-bg)">
                            <div class="h-full rounded-full" style="width:{{ $itemPct }}%;background:{{ $barColor }}"></div>
                        </div>
                        <span class="text-xs flex-shrink-0" style="color:var(--text-soft)">{{ $itemPct }}%</span>
                    </div>
                </div>

                <div class="lg:col-span-2 flex lg:block justify-between">
                    <span class="lg:hidden text-xs" style="color:var(--text-soft)">Allocated</span>
                    <p class="text-sm font-medium lg:text-right" style="color:var(--text-soft)">${{ number_format($alloc, 0) }}</p>
                </div>

                <div class="lg:col-span-2 flex lg:block justify-between">
                    <span class="lg:hidden text-xs" style="color:var(--text-soft)">Spent</span>
                    <p class="text-sm font-bold lg:text-right" style="color:{{ $isOver ? '#f87171' : '#34d399' }}">
                        ${{ number_format($actual, 0) }}
                        @if($isOver)<span class="text-xs ml-1">over!</span>@endif
                    </p>
                </div>

                <div class="lg:col-span-2 flex lg:block justify-between">
                    <span class="lg:hidden text-xs" style="color:var(--text-soft)">Left</span>
                    <p class="text-sm font-medium lg:text-right" style="color:{{ $left < 0 ? '#f87171' : 'var(--text-soft)' }}">
                        {{ $left < 0 ? '-' : '' }}${{ number_format(abs($left), 0) }}
                    </p>
                </div>

                <div class="lg:col-span-1 flex items-center gap-1">
                    <div class="relative flex-1">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs" style="color:var(--text-soft)">$</span>
                        <input type="number"
                               value="{{ $actual }}"
                               min="0" step="1"
                               wire:change="updateActual({{ $item->id }}, $event.target.value)"
                               title="Update actual spend"
                               class="w-full pl-5 pr-1 py-1.5 rounded-lg text-xs text-right focus:outline-none focus:ring-1 focus:ring-indigo-500"
                               style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="hidden lg:grid grid-cols-12 gap-2 px-5 py-3 text-sm font-semibold"
             style="background:var(--hover);border-top:1px solid var(--border)">
            <div class="col-span-5" style="color:var(--text-strong)">Total</div>
            <div class="col-span-2 text-right" style="color:var(--text-soft)">${{ number_format($summary['total_allocated'], 0) }}</div>
            <div class="col-span-2 text-right" style="color:{{ $summary['over_budget'] ? '#f87171' : '#34d399' }}">
                ${{ number_format($summary['total_actual'], 0) }}
            </div>
            <div class="col-span-2 text-right" style="color:{{ $summary['unallocated'] < 0 ? '#f87171' : 'var(--text-soft)' }}">
                {{ $summary['unallocated'] < 0 ? '-' : '' }}${{ number_format(abs($summary['unallocated']), 0) }}
            </div>
            <div class="col-span-1"></div>
        </div>
    </div>
    @endif

@else
    <div class="rounded-2xl border p-10 text-center" style="background:var(--panel);border-color:var(--border)">
        <p class="text-sm" style="color:var(--text-soft)">No budget set yet. Set a total budget to get started.</p>
    </div>
@endif
</div>