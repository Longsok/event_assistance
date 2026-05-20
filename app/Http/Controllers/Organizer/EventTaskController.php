<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTask;
use App\Models\TaskGroup;
use Illuminate\Http\Request;

class EventTaskController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizeEvent($event);

        $groups = TaskGroup::with(['eventTasks' => fn($q) =>
            $q->where('event_id', $event->id)
              ->orderBy('sort_order')
        ])->orderBy('sort_order')->get();

        $progress = [
            'total'     => $event->tasks()->count(),
            'completed' => $event->tasks()->where('status', 'done')->count(),
        ];

        return view('tasks.index', compact('event', 'groups', 'progress'));
    }

    // Add custom task
    public function store(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'task_name' => 'required|string|max:255',
            'group_id'  => 'required|exists:task_groups,id',
            'due_date'  => 'required|date',
            'priority'  => 'required|in:high,medium,low',
            'notes'     => 'nullable|string',
        ]);

        $dueDate = \Carbon\Carbon::parse($request->due_date);

        EventTask::create([
            'event_id'          => $event->id,
            'group_id'          => $request->group_id,
            'task_name'         => $request->task_name,
            'due_date'          => $dueDate,
            'original_due_date' => $dueDate,
            'priority'          => $request->priority,
            'notes'             => $request->notes,
            'status'            => $dueDate->isPast() ? 'overdue' : 'pending',
            'is_custom'         => true,
            'is_late'           => $dueDate->isPast(),
            'sort_order'        => EventTask::where('event_id', $event->id)
                                            ->where('group_id', $request->group_id)
                                            ->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Task added.');
    }

    public function update(Request $request, Event $event, EventTask $task)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'task_name' => 'required|string|max:255',
            'due_date'  => 'required|date',
            'priority'  => 'required|in:high,medium,low',
            'group_id'  => 'required|exists:task_groups,id',
        ]);

        $task->update($request->only([
            'task_name',
            'due_date',
            'priority',
            'group_id',
            'notes',
        ]));

        return back()->with('success', 'Task updated.');
    }

    // Mark task as done
    public function complete(Event $event, EventTask $task)
    {
        $this->authorizeEvent($event);

        $task->update([
            'status'       => 'done',
            'completed_by' => auth()->id(),
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Task marked as done.');
    }

    // Reopen task
    public function reopen(Event $event, EventTask $task)
    {
        $this->authorizeEvent($event);

        $task->update([
            'status'       => $task->due_date->isPast() ? 'overdue' : 'pending',
            'completed_by' => null,
            'completed_at' => null,
        ]);

        return back()->with('success', 'Task reopened.');
    }

    public function destroy(Event $event, EventTask $task)
    {
        $this->authorizeEvent($event);
        $task->delete();
        return back()->with('success', 'Task deleted.');
    }

    // Restore soft-deleted task
    public function restore(Event $event, int $taskId)
    {
        $this->authorizeEvent($event);
        EventTask::withTrashed()->findOrFail($taskId)->restore();
        return back()->with('success', 'Task restored.');
    }

    // Drag to reorder
    public function reorder(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        foreach ($request->order as $position => $id) {
            EventTask::where('id', $id)
                ->where('event_id', $event->id)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
