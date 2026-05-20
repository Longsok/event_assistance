<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventTask;
use App\Models\CategoryTemplate;
use App\Models\TaskGroup;
use Carbon\Carbon;

class TimelineEngine
{
<<<<<<< HEAD
=======
    /**
     * Generate timeline tasks for a newly created event.
     * Called automatically when organizer creates an event.
     */
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    public function generate(Event $event): void
    {
        $templates = CategoryTemplate::where('category_id', $event->category_id)
            ->with('group')
            ->orderBy('sort_order')
            ->get();

        foreach ($templates as $template) {
<<<<<<< HEAD
=======
            // Skip if trigger rule doesn't match this event
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
            if (!$this->passesRules($template->scale_trigger, $event)) {
                continue;
            }

            $dueDate = $this->resolveDate(
                $template,
                $event->start_date,
                $event->end_date,
                $event->total_days
            );

<<<<<<< HEAD
            $isLate   = $dueDate->isPast();
            $status   = $isLate ? 'overdue' : 'pending';
            $lateNote = $isLate
                ? "This task was originally due {$dueDate->diffForHumans()}. Handle immediately."
                : null;

=======
            $isLate    = $dueDate->isPast();
            $status    = $isLate ? 'overdue' : 'pending';
            $lateNote  = $isLate
                ? "This task was originally due {$dueDate->diffForHumans()}. Handle immediately."
                : null;

            // Reschedule overdue tasks to today
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
            $finalDate = $isLate ? Carbon::today() : $dueDate;

            EventTask::create([
                'event_id'          => $event->id,
                'group_id'          => $template->group_id,
                'task_name'         => $template->task_name,
                'original_due_date' => $dueDate,
                'due_date'          => $finalDate,
                'priority'          => $isLate ? 'high' : $template->priority,
                'status'            => $status,
                'is_custom'         => false,
                'is_late'           => $isLate,
                'late_note'         => $lateNote,
<<<<<<< HEAD
                'notes'             => $template->notes ?? null,
=======
                'notes'             => $template->notes,
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
                'sort_order'        => $template->sort_order,
            ]);
        }
    }

<<<<<<< HEAD
=======
    /**
     * Recalculate task due dates when event date changes.
     * Only affects pending/overdue tasks — done tasks are preserved.
     * Custom tasks with manually set dates are preserved too.
     */
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    public function recalculate(Event $event): void
    {
        $templates = CategoryTemplate::where('category_id', $event->category_id)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('task_name');

<<<<<<< HEAD
=======
        // Only recalculate system-generated tasks that are not done
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
        $pendingTasks = EventTask::where('event_id', $event->id)
            ->where('is_custom', false)
            ->whereNotIn('status', ['done', 'skipped'])
            ->get();

        foreach ($pendingTasks as $task) {
            $template = $templates->get($task->task_name);
            if (!$template) continue;

            $newDueDate = $this->resolveDate(
                $template,
                $event->start_date,
                $event->end_date,
                $event->total_days
            );

<<<<<<< HEAD
            $isLate    = $newDueDate->isPast();
=======
            $isLate   = $newDueDate->isPast();
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
            $finalDate = $isLate ? Carbon::today() : $newDueDate;

            $task->update([
                'original_due_date' => $newDueDate,
                'due_date'          => $finalDate,
                'status'            => $isLate ? 'overdue' : 'pending',
                'is_late'           => $isLate,
                'late_note'         => $isLate
                    ? "Rescheduled after event date change. Was due {$newDueDate->diffForHumans()}."
                    : null,
            ]);
        }
    }

<<<<<<< HEAD
=======
    /**
     * Preview how many tasks would be overdue/on-track
     * before the event is even created.
     * Used for the live warning on the create event form.
     */
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    public function preview(int $categoryId, Carbon $startDate, Event $eventPreview): array
    {
        $templates = CategoryTemplate::where('category_id', $categoryId)->get();

        $overdue = [];
        $onTrack = [];

        foreach ($templates as $template) {
            if (!$this->passesRules($template->scale_trigger, $eventPreview)) {
                continue;
            }

<<<<<<< HEAD
            $dueDate = $this->resolveDate($template, $startDate, $startDate, 1);
=======
            $dueDate = $this->resolveDate(
                $template,
                $startDate,
                $startDate, // single day preview
                1
            );
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882

            if ($dueDate->isPast()) {
                $overdue[] = [
                    'task_name' => $template->task_name,
                    'was_due'   => $dueDate->diffForHumans(),
                ];
            } else {
                $onTrack[] = [
                    'task_name' => $template->task_name,
                    'due_date'  => $dueDate->format('M d'),
                ];
            }
        }

        return [
            'overdue_count' => count($overdue),
            'ontrack_count' => count($onTrack),
            'overdue_tasks' => $overdue,
            'ontrack_tasks' => $onTrack,
        ];
    }

<<<<<<< HEAD
=======
    /**
     * Calculate the actual due date based on anchor type.
     */
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    private function resolveDate(
        CategoryTemplate $template,
        Carbon $startDate,
        Carbon $endDate,
        int $totalDays
    ): Carbon {
        return match ($template->anchor) {
            'before_event' =>
                $startDate->copy()->subDays(abs($template->days_before)),

            'first_day' =>
<<<<<<< HEAD
                $startDate->copy()->addDays($template->offset_days ?? 0),

            'last_day' =>
                $endDate->copy()->addDays($template->offset_days ?? 0),
=======
                $startDate->copy()->addDays($template->offset_days),

            'last_day' =>
                $endDate->copy()->addDays($template->offset_days),
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882

            'after_event' =>
                $endDate->copy()->addDays(abs($template->days_before)),

            'proportional' =>
                $startDate->copy()->addDays(
                    (int) round(
<<<<<<< HEAD
                        (($template->position_percent ?? 50) / 100) * ($totalDays - 1)
=======
                        ($template->position_percent / 100) * ($totalDays - 1)
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
                    )
                ),

            default => $startDate->copy(),
        };
    }

<<<<<<< HEAD
    private function passesRules(string $trigger, Event $event): bool
    {
        if ($trigger === 'any' || empty($trigger)) return true;

        if (str_contains($trigger, ' AND ')) {
            foreach (explode(' AND ', $trigger) as $rule) {
                if (!$this->evaluateRule(trim($rule), $event)) return false;
=======
    /**
     * Evaluate the scale_trigger rule against the event.
     *
     * Supported rules:
     *   any
     *   capacity > 200
     *   capacity <= 100
     *   venue = indoor
     *   venue = outdoor
     *   meal = yes
     *   days > 3
     */
    private function passesRules(string $trigger, Event $event): bool
    {
        if ($trigger === 'any' || empty($trigger)) {
            return true;
        }

        // Handle multiple rules joined by AND
        if (str_contains($trigger, ' AND ')) {
            foreach (explode(' AND ', $trigger) as $rule) {
                if (!$this->evaluateRule(trim($rule), $event)) {
                    return false;
                }
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
            }
            return true;
        }

<<<<<<< HEAD
        if (str_contains($trigger, ' OR ')) {
            foreach (explode(' OR ', $trigger) as $rule) {
                if ($this->evaluateRule(trim($rule), $event)) return true;
=======
        // Handle multiple rules joined by OR
        if (str_contains($trigger, ' OR ')) {
            foreach (explode(' OR ', $trigger) as $rule) {
                if ($this->evaluateRule(trim($rule), $event)) {
                    return true;
                }
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
            }
            return false;
        }

        return $this->evaluateRule($trigger, $event);
    }

<<<<<<< HEAD
    private function evaluateRule(string $rule, Event $event): bool
    {
        if (preg_match('/capacity\s*>\s*(\d+)/', $rule, $m))  return $event->capacity > (int) $m[1];
        if (preg_match('/capacity\s*<=\s*(\d+)/', $rule, $m)) return $event->capacity <= (int) $m[1];
        if (preg_match('/capacity\s*>=\s*(\d+)/', $rule, $m)) return $event->capacity >= (int) $m[1];
        if (preg_match('/capacity\s*<\s*(\d+)/', $rule, $m))  return $event->capacity < (int) $m[1];
        if (preg_match('/venue\s*=\s*(\w+)/', $rule, $m))     return $event->venue_type === $m[1];
        if (preg_match('/meal\s*=\s*(yes|no)/', $rule, $m))   return $event->meal_provided === ($m[1] === 'yes');
        if (preg_match('/days\s*>\s*(\d+)/', $rule, $m))      return $event->total_days > (int) $m[1];
        if (preg_match('/days\s*<=\s*(\d+)/', $rule, $m))     return $event->total_days <= (int) $m[1];
=======
    /**
     * Evaluate a single rule string against the event.
     */
    private function evaluateRule(string $rule, Event $event): bool
    {
        // capacity > X
        if (preg_match('/capacity\s*>\s*(\d+)/', $rule, $matches)) {
            return $event->capacity > (int) $matches[1];
        }

        // capacity <= X
        if (preg_match('/capacity\s*<=\s*(\d+)/', $rule, $matches)) {
            return $event->capacity <= (int) $matches[1];
        }

        // capacity >= X
        if (preg_match('/capacity\s*>=\s*(\d+)/', $rule, $matches)) {
            return $event->capacity >= (int) $matches[1];
        }

        // capacity < X
        if (preg_match('/capacity\s*<\s*(\d+)/', $rule, $matches)) {
            return $event->capacity < (int) $matches[1];
        }

        // venue = indoor/outdoor/hybrid
        if (preg_match('/venue\s*=\s*(\w+)/', $rule, $matches)) {
            return $event->venue_type === $matches[1];
        }

        // meal = yes/no
        if (preg_match('/meal\s*=\s*(yes|no)/', $rule, $matches)) {
            return $event->meal_provided === ($matches[1] === 'yes');
        }

        // days > X (event duration)
        if (preg_match('/days\s*>\s*(\d+)/', $rule, $matches)) {
            return $event->total_days > (int) $matches[1];
        }

        // days <= X
        if (preg_match('/days\s*<=\s*(\d+)/', $rule, $matches)) {
            return $event->total_days <= (int) $matches[1];
        }

        // Unknown rule — include by default
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
        return true;
    }
}
