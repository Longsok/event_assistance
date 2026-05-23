<x-app-layout>
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-6">
    <div>
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center gap-1 text-indigo-600 text-sm hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ $event->title }}
        </a>
        <h2 class="text-2xl font-bold text-slate-900 mt-1">Task Checklist</h2>
    </div>
    <livewire:task-checklist :event="$event" />
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Add Custom Task</h3>
        <form method="POST" action="{{ route('events.tasks.store', $event) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-xs text-slate-500 mb-1">Task Name *</label>
                <input type="text" name="task_name" placeholder="e.g. Confirm catering headcount" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Group *</label>
                <select name="group_id" required class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                    <option value="">Select group...</option>
                    @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Due Date *</label>
                <input type="date" name="due_date" required
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Priority</label>
                <select name="priority" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="low">Low</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Notes</label>
                <input type="text" name="notes" placeholder="Optional notes..."
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Add Task</button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
