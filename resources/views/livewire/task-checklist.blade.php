<div>
    {{-- Progress --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-4">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold text-slate-800">Task Progress</p>
            <p class="text-sm text-slate-500">
                <span class="font-bold text-indigo-600">{{ $progress['done'] }}</span> / {{ $progress['total'] }} done
                @if($progress['overdue'] > 0)
                <span class="text-red-500 ml-2">{{ $progress['overdue'] }} overdue</span>
                @endif
            </p>
        </div>
        <div class="bg-slate-100 rounded-full h-2.5 overflow-hidden">
            <div class="h-full bg-indigo-600 rounded-full transition-all" style="width:{{ $progress['pct'] }}%"></div>
        </div>
    </div>

    {{-- Groups --}}
    @forelse($groups as $idx => $group)
    @php
        $tasks      = collect($group['tasks']);
        $done       = $tasks->where('status','done')->count();
        $total      = $tasks->count();
        $overdueCnt = $tasks->where('status','overdue')->count();
        $isOpen     = in_array($idx, $expandedGroups);
        $allDone    = $done === $total && $total > 0;
    @endphp

    <div wire:key="group-{{ $idx }}" class="mb-3 bg-white rounded-2xl border {{ $overdueCnt > 0 ? 'border-red-200' : 'border-slate-200' }} shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 py-4 cursor-pointer {{ $overdueCnt > 0 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-slate-50' }} transition select-none"
             wire:click="toggleGroup({{ $idx }})">
            <div class="w-3 h-3 rounded-full flex-shrink-0 {{ $allDone ? 'bg-emerald-500' : ($overdueCnt > 0 ? 'bg-red-400' : 'bg-indigo-400') }}"></div>
            <span class="flex-1 font-semibold text-slate-800 text-sm">{{ $group['label'] }}</span>
            @if($overdueCnt > 0)
            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">{{ $overdueCnt }} overdue</span>
            @endif
            <span class="text-xs text-slate-400">{{ $done }}/{{ $total }}</span>
            <svg class="w-4 h-4 text-slate-400 transition-transform {{ $isOpen ? 'rotate-180' : '' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        {{-- Task rows --}}
        @if($isOpen)
        @foreach($group['tasks'] as $tIdx => $task)
        <div wire:key="task-{{ $task['id'] }}"
             class="flex items-center gap-4 px-5 py-3.5 border-t border-slate-100 {{ $task['status'] === 'overdue' ? 'bg-red-50' : 'hover:bg-slate-50' }} transition">

            {{-- Checkbox --}}
            <div wire:click="toggleTask({{ $task['id'] }})"
                 class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center cursor-pointer transition
                        {{ $task['status'] === 'done'
                            ? 'bg-emerald-500 border-emerald-500'
                            : ($task['status'] === 'overdue'
                                ? 'border-red-400 hover:border-red-600'
                                : 'border-slate-300 hover:border-indigo-500') }}">
                @if($task['status'] === 'done')
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                @endif
            </div>

            {{-- Name + group --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate {{ $task['status'] === 'done' ? 'line-through text-slate-400' : 'text-slate-800' }}">
                    {{ $task['task_name'] }}
                </p>
                @if($task['group_name'])
                <p class="text-xs text-slate-400 mt-0.5">{{ $task['group_name'] }}</p>
                @endif
            </div>

            {{-- Priority --}}
            <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                {{ $task['priority'] === 'high' ? 'bg-red-100 text-red-700' : ($task['priority'] === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500') }}">
                {{ $task['priority'] }}
            </span>

            {{-- Date --}}
            <span class="text-xs font-medium w-12 text-right flex-shrink-0 {{ $task['status'] === 'overdue' ? 'text-red-500' : ($task['status'] === 'done' ? 'text-slate-300' : 'text-slate-400') }}">
                {{ \Carbon\Carbon::parse($task['due_date'])->format('M d') }}
            </span>
        </div>
        @endforeach
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 py-12 text-center text-slate-400 text-sm">No tasks yet.</div>
    @endforelse
</div>
