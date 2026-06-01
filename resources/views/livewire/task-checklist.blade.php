<div>
    {{-- Progress --}}
    <div class="rounded-2xl border p-5 mb-4" style="background:var(--panel);border-color:var(--border)">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold" style="color:var(--text-strong)">Task Progress</p>
            <p class="text-sm" style="color:var(--text-soft)">
                <span class="font-bold" style="color:#818cf8">{{ $progress['done'] }}</span>
                / {{ $progress['total'] }} done
                @if($progress['overdue'] > 0)
                <span class="ml-2" style="color:#f87171">{{ $progress['overdue'] }} overdue</span>
                @endif
            </p>
        </div>
        <div class="rounded-full h-2.5 overflow-hidden" style="background:var(--input-bg)">
            <div class="h-full rounded-full transition-all" style="width:{{ $progress['pct'] }}%;background:linear-gradient(90deg,#4f46e5,#7c3aed)"></div>
        </div>
    </div>

    {{-- Add Task --}}
    <div class="rounded-2xl border p-4 mb-4" style="background:var(--panel);border-color:var(--border)">
        <button type="button" wire:click="$toggle('showAddForm')"
                class="w-full flex items-center justify-center gap-2 py-2 text-sm font-medium rounded-xl transition"
                style="background:rgba(99,102,241,.12);color:#818cf8;border:1px solid rgba(99,102,241,.25)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ $showAddForm ? 'Cancel' : 'Add New Task' }}
        </button>

        @if($showAddForm)
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Task Name *</label>
                <input type="text" wire:model="newTaskName" placeholder="e.g. Confirm catering headcount"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                @error('newTaskName')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Group *</label>
                <select wire:model="newTaskGroup"
                        class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    <option value="" style="background:var(--panel)">Select group...</option>
                    @foreach($taskGroups as $g)
                    <option value="{{ $g->id }}" style="background:var(--panel)">{{ $g->name }}</option>
                    @endforeach
                </select>
                @error('newTaskGroup')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Due Date *</label>
                <input type="date" wire:model="newTaskDue"
                       class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                @error('newTaskDue')<p class="text-xs mt-1" style="color:#f87171">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs mb-1" style="color:var(--text-soft)">Priority</label>
                <select wire:model="newTaskPriority"
                        class="w-full rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    <option value="medium" style="background:var(--panel)">Medium</option>
                    <option value="high" style="background:var(--panel)">High</option>
                    <option value="low" style="background:var(--panel)">Low</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="button" wire:click="addTask"
                        class="w-full py-2.5 text-white text-sm font-medium rounded-xl"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    Add Task
                </button>
            </div>
        </div>
        @endif
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

    <div wire:key="group-{{ $idx }}" class="mb-3 rounded-2xl overflow-hidden border"
         style="background:var(--panel);border-color:{{ $overdueCnt > 0 ? 'rgba(239,68,68,.3)' : 'var(--border)' }}">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 py-4 cursor-pointer select-none transition"
             style="{{ $overdueCnt > 0 ? 'background:rgba(239,68,68,.08)' : '' }}"
             onmouseover="this.style.background='var(--hover)'"
             onmouseout="this.style.background='{{ $overdueCnt > 0 ? 'rgba(239,68,68,.08)' : 'transparent' }}'"
             wire:click="toggleGroup({{ $idx }})">
            <div class="w-3 h-3 rounded-full flex-shrink-0"
                 style="background:{{ $allDone ? '#10b981' : ($overdueCnt > 0 ? '#f87171' : '#818cf8') }}"></div>
            <span class="flex-1 font-semibold text-sm" style="color:var(--text-strong)">{{ $group['label'] }}</span>
            @if($overdueCnt > 0)
            <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(239,68,68,.15);color:#f87171">
                {{ $overdueCnt }} overdue
            </span>
            @endif
            <span class="text-xs" style="color:var(--text-soft)">{{ $done }}/{{ $total }}</span>
            <svg class="w-4 h-4 transition-transform {{ $isOpen ? 'rotate-180' : '' }}" style="color:var(--text-soft)"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        @if($isOpen)
        @foreach($group['tasks'] as $tIdx => $task)
        <div wire:key="task-{{ $task['id'] }}"
             class="flex items-center gap-4 px-5 py-3.5 transition cursor-pointer"
             style="border-top:1px solid var(--border-soft);{{ $task['status']==='overdue' ? 'background:rgba(239,68,68,.05)' : '' }}"
             onmouseover="this.style.background='var(--hover)'"
             onmouseout="this.style.background='{{ $task['status']==='overdue' ? 'rgba(239,68,68,.05)' : 'transparent' }}'">

            <div wire:click="toggleTask({{ $task['id'] }})"
                 class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                 style="{{ $task['status']==='done' ? 'background:#10b981;border-color:#10b981' : ($task['status']==='overdue' ? 'border-color:#f87171' : 'border-color:var(--input-border)') }}">
                @if($task['status'] === 'done')
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate {{ $task['status']==='done' ? 'line-through' : '' }}"
                   style="{{ $task['status']==='done' ? 'color:var(--text-soft)' : 'color:var(--text-strong)' }}">
                    {{ $task['task_name'] }}
                </p>
                @if($task['group_name'])
                <p class="text-xs mt-0.5" style="color:#818cf8">{{ $task['group_name'] }}</p>
                @endif
            </div>

            <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0"
                  style="{{ $task['priority']==='high' ? 'background:rgba(239,68,68,.12);color:#f87171' : ($task['priority']==='medium' ? 'background:rgba(251,191,36,.1);color:#fbbf24' : 'background:var(--hover);color:var(--text-soft)') }}">
                {{ $task['priority'] }}
            </span>

            <span class="text-xs font-medium w-12 text-right flex-shrink-0"
                  style="{{ $task['status']==='overdue' ? 'color:#f87171' : ($task['status']==='done' ? 'color:var(--text-soft)' : 'color:var(--text-soft)') }}">
                {{ \Carbon\Carbon::parse($task['due_date'])->format('M d') }}
            </span>
        </div>
        @endforeach
        @endif
    </div>
    @empty
    <div class="rounded-2xl border py-12 text-center text-sm" style="background:var(--panel);border-color:var(--border);color:var(--text-soft)">
        No tasks yet.
    </div>
    @endforelse
</div>