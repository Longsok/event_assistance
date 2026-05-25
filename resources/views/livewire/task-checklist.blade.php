<div>
    {{-- Progress --}}
    <div class="rounded-2xl border p-5 mb-4" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold text-white">Task Progress</p>
            <p class="text-sm" style="color:#6b7280">
                <span class="font-bold" style="color:#818cf8">{{ $progress['done'] }}</span>
                / {{ $progress['total'] }} done
                @if($progress['overdue'] > 0)
                <span class="ml-2" style="color:#f87171">{{ $progress['overdue'] }} overdue</span>
                @endif
            </p>
        </div>
        <div class="rounded-full h-2.5 overflow-hidden" style="background:rgba(255,255,255,.08)">
            <div class="h-full rounded-full transition-all" style="width:{{ $progress['pct'] }}%;background:linear-gradient(90deg,#4f46e5,#7c3aed)"></div>
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

    <div wire:key="group-{{ $idx }}" class="mb-3 rounded-2xl overflow-hidden border"
         style="background:#0d1117;border-color:{{ $overdueCnt > 0 ? 'rgba(239,68,68,.3)' : 'rgba(255,255,255,.07)' }}">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 py-4 cursor-pointer select-none transition"
             style="{{ $overdueCnt > 0 ? 'background:rgba(239,68,68,.08)' : '' }}"
             onmouseover="this.style.background='rgba(255,255,255,.03)'"
             onmouseout="this.style.background='{{ $overdueCnt > 0 ? 'rgba(239,68,68,.08)' : 'transparent' }}'"
             wire:click="toggleGroup({{ $idx }})">
            <div class="w-3 h-3 rounded-full flex-shrink-0"
                 style="background:{{ $allDone ? '#10b981' : ($overdueCnt > 0 ? '#f87171' : '#818cf8') }}"></div>
            <span class="flex-1 font-semibold text-white text-sm">{{ $group['label'] }}</span>
            @if($overdueCnt > 0)
            <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(239,68,68,.15);color:#f87171">
                {{ $overdueCnt }} overdue
            </span>
            @endif
            <span class="text-xs" style="color:#6b7280">{{ $done }}/{{ $total }}</span>
            <svg class="w-4 h-4 transition-transform {{ $isOpen ? 'rotate-180' : '' }}" style="color:#6b7280"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        @if($isOpen)
        @foreach($group['tasks'] as $tIdx => $task)
        <div wire:key="task-{{ $task['id'] }}"
             class="flex items-center gap-4 px-5 py-3.5 transition cursor-pointer"
             style="border-top:1px solid rgba(255,255,255,.05);{{ $task['status']==='overdue' ? 'background:rgba(239,68,68,.05)' : '' }}"
             onmouseover="this.style.background='rgba(255,255,255,.02)'"
             onmouseout="this.style.background='{{ $task['status']==='overdue' ? 'rgba(239,68,68,.05)' : 'transparent' }}'">

            <div wire:click="toggleTask({{ $task['id'] }})"
                 class="flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                 style="{{ $task['status']==='done' ? 'background:#10b981;border-color:#10b981' : ($task['status']==='overdue' ? 'border-color:#f87171' : 'border-color:rgba(255,255,255,.2)') }}">
                @if($task['status'] === 'done')
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate {{ $task['status']==='done' ? 'line-through' : 'text-white' }}"
                   style="{{ $task['status']==='done' ? 'color:#6b7280' : '' }}">
                    {{ $task['task_name'] }}
                </p>
                @if($task['group_name'])
                <p class="text-xs mt-0.5" style="color:#818cf8">{{ $task['group_name'] }}</p>
                @endif
            </div>

            <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0"
                  style="{{ $task['priority']==='high' ? 'background:rgba(239,68,68,.12);color:#f87171' : ($task['priority']==='medium' ? 'background:rgba(251,191,36,.1);color:#fbbf24' : 'background:rgba(255,255,255,.07);color:#9ca3af') }}">
                {{ $task['priority'] }}
            </span>

            <span class="text-xs font-medium w-12 text-right flex-shrink-0"
                  style="{{ $task['status']==='overdue' ? 'color:#f87171' : ($task['status']==='done' ? 'color:#374151' : 'color:#6b7280') }}">
                {{ \Carbon\Carbon::parse($task['due_date'])->format('M d') }}
            </span>
        </div>
        @endforeach
        @endif
    </div>
    @empty
    <div class="rounded-2xl border py-12 text-center text-sm" style="background:#0d1117;border-color:rgba(255,255,255,.07);color:#6b7280">
        No tasks yet.
    </div>
    @endforelse
</div>
