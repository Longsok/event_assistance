<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\TaskGroup;
use App\Models\EventTask;
use Livewire\Component;
use Carbon\Carbon;

class TaskChecklist extends Component
{
    public Event $event;
    public array $expandedGroups = [];

    // Inline "Add Task" form fields
    public string $newTaskName = '';
    public string $newTaskGroup = '';
    public string $newTaskDue = '';
    public string $newTaskPriority = 'medium';
    public bool $showAddForm = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
        $groups = $this->buildGroups();
        $autoOpen = [];
        foreach ($groups as $index => $group) {
            if (collect($group['tasks'])->whereNotIn('status', ['done'])->isNotEmpty() && count($autoOpen) < 3) {
                $autoOpen[] = $index;
            }
        }
        $this->expandedGroups = $autoOpen;
    }

    public function toggleGroup(int $index): void
    {
        if (in_array($index, $this->expandedGroups)) {
            $this->expandedGroups = array_values(array_filter($this->expandedGroups, fn($i) => $i !== $index));
        } else {
            $this->expandedGroups[] = $index;
        }
    }

    public function toggleTask(int $taskId): void
    {
        $task = $this->event->tasks()->findOrFail($taskId);
        $isDone = $task->status === 'done';
        $task->update([
            'status'       => $isDone ? 'pending' : 'done',
            'completed_by' => $isDone ? null : auth()->id(),
            'completed_at' => $isDone ? null : now(),
        ]);
    }

    public function buildGroups(): array
    {
        $tasks = $this->event->tasks()->with('group')->orderBy('due_date')->get();
        $today      = Carbon::today();
        $eventStart = Carbon::parse($this->event->start_date);
        $eventEnd   = Carbon::parse($this->event->end_date);
        $buckets    = [];
        $doneTasks  = [];

        foreach ($tasks as $task) {
            $due = Carbon::parse($task->due_date);
            $row = [
                'id'         => $task->id,
                'task_name'  => $task->task_name,
                'due_date'   => $task->due_date,
                'priority'   => $task->priority,
                'status'     => $task->status,
                'group_name' => $task->group?->name,
            ];

            if ($task->status === 'done') { $doneTasks[] = $row; continue; }
            if ($due->lt($today)) { $buckets['overdue']['label'] = 'Overdue'; $buckets['overdue']['tasks'][] = $row; continue; }
            if ($due->isToday()) { $buckets['today']['label'] = 'Today ' . $today->format('M d'); $buckets['today']['tasks'][] = $row; continue; }
            if ($due->isTomorrow()) { $buckets['tomorrow']['label'] = 'Tomorrow ' . $due->format('M d'); $buckets['tomorrow']['tasks'][] = $row; continue; }
            if ($due->between($eventStart, $eventEnd)) {
                $dayNum = $eventStart->diffInDays($due) + 1;
                $key = 'day_' . $dayNum;
                $buckets[$key]['label'] = 'Day ' . $dayNum . ' - ' . $due->format('D M d');
                $buckets[$key]['tasks'][] = $row;
                continue;
            }
            if ($due->lt($eventStart)) { $key = 'pre_' . $due->format('Ymd'); $buckets[$key]['label'] = 'Pre-event ' . $due->format('M d'); $buckets[$key]['tasks'][] = $row; continue; }
            $key = 'post_' . $due->format('Ymd');
            $buckets[$key]['label'] = 'Post-event ' . $due->format('M d');
            $buckets[$key]['tasks'][] = $row;
        }

        if (!empty($doneTasks)) {
            $buckets['done']['label'] = 'Completed (' . count($doneTasks) . ')';
            $buckets['done']['tasks'] = $doneTasks;
        }

        return array_values($buckets);
    }

    public function addTask(): void
    {
        $this->validate([
            'newTaskName'     => 'required|string|max:255',
            'newTaskGroup'    => 'required|exists:task_groups,id',
            'newTaskDue'      => 'required|date',
            'newTaskPriority' => 'required|in:high,medium,low',
        ], [], [
            'newTaskName'  => 'task name',
            'newTaskGroup' => 'group',
            'newTaskDue'   => 'due date',
        ]);

        $due = Carbon::parse($this->newTaskDue);

        EventTask::create([
            'event_id'          => $this->event->id,
            'group_id'          => $this->newTaskGroup,
            'task_name'         => $this->newTaskName,
            'due_date'          => $due,
            'original_due_date' => $due,
            'priority'          => $this->newTaskPriority,
            'status'            => $due->isPast() ? 'overdue' : 'pending',
            'is_custom'         => true,
            'is_late'           => $due->isPast(),
            'sort_order'        => EventTask::where('event_id', $this->event->id)
                                            ->where('group_id', $this->newTaskGroup)
                                            ->max('sort_order') + 1,
        ]);

        // Reset the form
        $this->reset(['newTaskName', 'newTaskGroup', 'newTaskDue', 'newTaskPriority']);
        $this->newTaskPriority = 'medium';
        $this->showAddForm = false;
    }

    public function render()
    {
        $groups  = $this->buildGroups();
        $tasks   = $this->event->tasks()->get();
        $total   = $tasks->count();
        $done    = $tasks->where('status', 'done')->count();
        $overdue = $tasks->where('status', 'overdue')->count();
        $pct     = $total > 0 ? round(($done / $total) * 100) : 0;

        return view('livewire.task-checklist', [
            'groups'         => $groups,
            'expandedGroups' => $this->expandedGroups,
            'progress'       => compact('total', 'done', 'overdue', 'pct'),
            'taskGroups'     => TaskGroup::orderBy('sort_order')->get(),
        ]);
    }
}