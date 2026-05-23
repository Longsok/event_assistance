<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-2xl font-bold text-slate-900 mt-1">Budget Tracker</h2>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-3">Total Budget</h3>
        <form method="POST" action="{{ route('events.budget.update', $event) }}" class="flex items-end gap-4">
            @csrf @method('PATCH')
            <div class="flex-1">
                <label class="block text-sm text-slate-500 mb-1">Amount</label>
                <input type="number" name="total_budget" value="{{ $budget?->total_budget ?? 0 }}" min="0" step="0.01"
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Update</button>
        </form>
    </div>
    <livewire:budget-tracker :event="$event" />
    @if($budget)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Add Budget Item</h3>
        <form method="POST" action="{{ route('events.budget.items.store', $event) }}" class="flex flex-wrap gap-3">
            @csrf
            <div class="flex-1 min-w-48">
                <input type="text" name="line_item" placeholder="Item name (e.g. Catering)" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <div class="w-36">
                <input type="number" name="allocated_amount" placeholder="Amount" min="0" step="0.01" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-xl transition">Add Item</button>
        </form>
    </div>
    @endif
</div>
</x-app-layout>
