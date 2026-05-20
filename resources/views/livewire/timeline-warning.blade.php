<div>
    @if($showWarning && count($preview['overdue_tasks'] ?? []) > 0)
    <div class="mt-4 bg-red-50 border border-red-200 rounded-2xl p-4">
        <div class="flex-1">
            <p class="font-semibold text-red-700 text-sm">
                {{ $preview['overdue_count'] }} task{{ $preview['overdue_count'] > 1 ? 's' : '' }} already overdue for this date
            </p>
            <div class="space-y-1 mt-2">
                @foreach(array_slice($preview['overdue_tasks'], 0, 5) as $task)
                <div class="flex items-center gap-2 text-xs text-red-600">
                    <span class="w-1.5 h-1.5 bg-red-400 rounded-full"></span>
                    <span class="font-medium">{{ $task['task_name'] }}</span>
                    <span class="text-red-400">— {{ $task['was_due'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @elseif(!empty($preview) && ($preview['ontrack_count'] ?? 0) > 0)
    <div class="mt-4 bg-green-50 border border-green-200 rounded-2xl p-4">
        <p class="text-sm text-green-700 font-medium">
            All {{ $preview['ontrack_count'] }} tasks are on track for this date.
        </p>
    </div>
    @endif
</div>
