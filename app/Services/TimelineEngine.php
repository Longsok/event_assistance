<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventTask;
use App\Models\CategoryTemplate;
use App\Models\TaskGroup;
use Carbon\Carbon;

class TimelineEngine
{
    public function generate(Event $event): void
    {
        $templates = CategoryTemplate::where('category_id', $event->category_id)
            ->with('group')
            ->orderBy('sort_order')
            ->get();

        foreach ($templates as $template) {
            if (!$this->passesRules($template->scale_trigger, $event)) {
                continue;
            }

            $dueDate = $this->resolveDate(
                $template,
                $event->start_date,
                $event->end_date,
                $event->total_days
            );

            $isLate   = $dueDate->isPast();
            $status   = $isLate ? 'overdue' : 'pending';
            $lateNote = $isLate
                ? "This task was originally due {$dueDate->diffForHumans()}. Handle immediately."
                : null;

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
                'notes'             => $template->notes ?? null,
                'sort_order'        => $template->sort_order,
            ]);
        }
    }

    public function recalculate(Event $event): void
    {
        $templates = CategoryTemplate::where('category_id', $event->category_id)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('task_name');

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

            $isLate    = $newDueDate->isPast();
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

    public function preview(int $categoryId, Carbon $startDate, Event $eventPreview): array
    {
        $templates = CategoryTemplate::where('category_id', $categoryId)->get();

        $overdue = [];
        $onTrack = [];

        foreach ($templates as $template) {
            if (!$this->passesRules($template->scale_trigger, $eventPreview)) {
                continue;
            }

            $dueDate = $this->resolveDate($template, $startDate, $startDate, 1);

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
                $startDate->copy()->addDays($template->offset_days ?? 0),

            'last_day' =>
                $endDate->copy()->addDays($template->offset_days ?? 0),

            'after_event' =>
                $endDate->copy()->addDays(abs($template->days_before)),

            'proportional' =>
                $startDate->copy()->addDays(
                    (int) round(
                        (($template->position_percent ?? 50) / 100) * ($totalDays - 1)
                    )
                ),

            default => $startDate->copy(),
        };
    }

    private function passesRules(string $trigger, Event $event): bool
    {
        if ($trigger === 'any' || empty($trigger)) return true;

        if (str_contains($trigger, ' AND ')) {
            foreach (explode(' AND ', $trigger) as $rule) {
                if (!$this->evaluateRule(trim($rule), $event)) return false;
            }
            return true;
        }

        if (str_contains($trigger, ' OR ')) {
            foreach (explode(' OR ', $trigger) as $rule) {
                if ($this->evaluateRule(trim($rule), $event)) return true;
            }
            return false;
        }

        return $this->evaluateRule($trigger, $event);
    }

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
        return true;
    }
}
