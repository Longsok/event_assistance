<div class="space-y-4">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold text-gray-800">Task Progress</p>
            <p class="text-sm text-gray-500">
                <span class="font-bold text-indigo-600">{{ $progress['completed'] }}</span> / {{ $progress['total'] }} completed
            </p>
        </div>
        @php $pct = $progress['total'] > 0 ? round(($progress['completed'] / $progress['total']) * 100) : 0; @endphp
        <div class="bg-gray-100 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full bg-indigo-600 transition-all duration-500" style="width: {{ $pct }}%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1.5 text-right">{{ $pct }}% done</p>
    </div>
    @foreach($groups as $group)
    @if($group->eventTasks->count())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <button wire:click="toggleGroup({{ $group->id }})"
                class="w-full flex items-center gap-3 px-5 py-4 border-b border-gray-100 hover:bg-gray-50 transition text-left">
            <div class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $group->color }}"></div>
            <span class="font-semibold text-gray-800 flex-1">{{ $group->name }}</span>
            <span class="text-xs text-gray-400">{{ $group->eventTasks->where('status','done')->count() }}/{{ $group->eventTasks->count() }}</span>
        </button>
        @if(in_array($group->id, $expandedGroups))
        @foreach($group->eventTasks as $task)
        <div class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-100 last:border-0
                    {{ $task->status === 'overdue' ? 'bg-red-50' : 'hover:bg-gray-50' }} transition">
            <button wire:click="toggleTask({{ $task->id }})"
                    class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition
                           {{ $task->status === 'done' ? 'bg-green-500 border-green-500 text-white' :
                              ($task->status === 'overdue' ? 'border-red-400' : 'border-gray-300 hover:border-indigo-500') }}">
                @if($task->status === 'done')
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                @endif
            </button>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate {{ $task->status === 'done' ? 'line-through text-gray-400' : 'text-gray-800' }}">
                    {{ $task->task_name }}
                </p>
                @if($task->notes)
                <p class="text-xs text-gray-400 truncate mt-0.5">{{ $task->notes }}</p>
                @endif
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0
                {{ $task->priority === 'high' ? 'bg-red-100 text-red-600' :
                   ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-500') }}">
                {{ $task->priority }}
            </span>
            <span class="text-xs flex-shrink-0 {{ $task->status === 'overdue' ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                {{ $task->due_date->format('M d') }}
            </span>
        </div>
        @endforeach
        @endif
    </div>
    @endif
    @endforeach
</div>
