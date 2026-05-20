<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventTask;
use App\Models\TaskGroup;
use Livewire\Component;

class TaskChecklist extends Component
{
    public Event $event;
    public array $expandedGroups = [];

    public function mount(Event $event): void
    {
        $this->event = $event;
        // Expand all groups by default
        $this->expandedGroups = TaskGroup::pluck('id')->toArray();
    }

    /**
     * Toggle task done/pending.
     */
    public function toggleTask(int $taskId): void
    {
        $task = EventTask::findOrFail($taskId);

        if ($task->status === 'done') {
            // Reopen
            $task->update([
                'status'       => $task->due_date->isPast() ? 'overdue' : 'pending',
                'completed_by' => null,
                'completed_at' => null,
            ]);
        } else {
            // Mark done
            $task->update([
                'status'       => 'done',
                'completed_by' => auth()->id(),
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Toggle group expand/collapse.
     */
    public function toggleGroup(int $groupId): void
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_filter(
                $this->expandedGroups,
                fn($id) => $id !== $groupId
            );
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    public function render()
    {
        $groups = TaskGroup::with(['eventTasks' => fn($q) =>
            $q->where('event_id', $this->event->id)
              ->orderBy('sort_order')
        ])->orderBy('sort_order')->get();

        $progress = [
            'total'     => EventTask::where('event_id', $this->event->id)->count(),
            'completed' => EventTask::where('event_id', $this->event->id)
                                    ->where('status', 'done')->count(),
        ];

        return view('livewire.task-checklist', compact('groups', 'progress'));
    }
}
