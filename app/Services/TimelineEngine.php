<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventTask;
use Carbon\Carbon;

class TimelineEngine
{
    /**
     * Generate tasks for an event based on its category templates.
     * Tasks are spread proportionally across the planning window
     * (from today or 90 days before, whichever is earlier, to event start).
     */
    public function generate(Event $event): void
    {
        $templates = $event->category?->categoryTemplates()->with('group')->get();

        if (!$templates || $templates->isEmpty()) {
            return;
        }

        $eventStart = Carbon::parse($event->start_date);
        $eventEnd   = Carbon::parse($event->end_date);
        $durationDays = $eventStart->diffInDays($eventEnd) + 1; // e.g. 10 for a 10-day event

        // Planning window: how many days before the event starts do we plan?
        // Use up to 90 days before, but at least 14.
        $planningDays = max(14, min(90, $eventStart->diffInDays(now())));

        foreach ($templates as $template) {
            // Check scale_trigger conditions
            if (!$this->passesTrigger($template, $event)) {
                continue;
            }

            $dueDate = $this->calculateDueDate(
                $template,
                $eventStart,
                $eventEnd,
                $durationDays,
                $planningDays
            );

            $isLate = $dueDate->isPast();

            EventTask::create([
                'event_id'          => $event->id,
                'group_id'          => $template->group_id,
                'task_name'         => $template->task_name,
                'original_due_date' => $dueDate,
                'due_date'          => $dueDate,
                'priority'          => $template->priority ?? 'medium',
                'status'            => $isLate ? 'overdue' : 'pending',
                'is_custom'         => false,
                'is_late'           => $isLate,
                'late_note'         => $isLate
                    ? "Task was overdue when event was created (due {$dueDate->format('M d')})"
                    : null,
                'notes'             => $template->notes ?? null,
            ]);
        }

        // For multi-day events (>1 day), also generate per-day tasks
        if ($durationDays > 1) {
            $this->generateDailyTasks($event, $eventStart, $durationDays);
        }
    }

    /**
     * Calculate the due date for a task template based on its anchor type.
     *
     * Anchor types:
     *   before_event   → event_start - days_before
     *   first_day      → event_start + offset_days
     *   last_day       → event_end - offset_days
     *   after_event    → event_end + days_before
     *   proportional   → distributed across planning window by position_percent
     */
    protected function calculateDueDate(
        $template,
        Carbon $eventStart,
        Carbon $eventEnd,
        int $durationDays,
        int $planningDays
    ): Carbon {
        $daysBeforeRaw = (int)($template->days_before ?? 7);
        $offsetDays    = (int)($template->offset_days ?? 0);
        $positionPct   = (int)($template->position_percent ?? 50);

        switch ($template->anchor ?? 'before_event') {

            // "30 days before the event starts"
            case 'before_event':
                return $eventStart->copy()->subDays(max(0, $daysBeforeRaw));

            // "On the first day of the event, offset_days after it starts"
            case 'first_day':
                return $eventStart->copy()->addDays($offsetDays);

            // "On the last day of the event, offset_days before it ends"
            case 'last_day':
                return $eventEnd->copy()->subDays($offsetDays);

            // "N days after the event ends"
            case 'after_event':
                return $eventEnd->copy()->addDays(max(0, $daysBeforeRaw));

            // "At position X% through the planning window"
            // e.g. position_percent=50 → halfway between planningStart and eventStart
            case 'proportional':
                $planningStart = $eventStart->copy()->subDays($planningDays);
                $daysFromStart = (int)round($planningDays * $positionPct / 100);
                return $planningStart->copy()->addDays($daysFromStart);

            default:
                return $eventStart->copy()->subDays(max(1, $daysBeforeRaw));
        }
    }

    /**
     * For multi-day events, generate standard daily tasks:
     * venue check, meal prep confirmation, etc.
     */
    protected function generateDailyTasks(Event $event, Carbon $eventStart, int $durationDays): void
    {
        // Find the most suitable group for daily tasks (fallback: first group)
        $group = $event->tasks()->with('group')->first()?->group
            ?? \App\Models\TaskGroup::first();

        if (!$group) return;

        $dailyTaskTemplates = [
            ['name' => 'Venue setup check',          'time' => '08:00', 'priority' => 'high'],
            ['name' => 'Guest arrival coordination',  'time' => '09:00', 'priority' => 'high'],
            ['name' => 'Confirm meal service',        'time' => '10:00', 'priority' => 'medium'],
            ['name' => 'End-of-day debrief',          'time' => '18:00', 'priority' => 'low'],
        ];

        // Only generate for the first 5 days max to avoid flooding
        $daysToGenerate = min($durationDays, 5);

        for ($day = 1; $day <= $daysToGenerate; $day++) {
            $date = $eventStart->copy()->addDays($day - 1);

            foreach ($dailyTaskTemplates as $t) {
                EventTask::create([
                    'event_id'          => $event->id,
                    'group_id'          => $group->id,
                    'task_name'         => "Day {$day}: " . $t['name'],
                    'original_due_date' => $date,
                    'due_date'          => $date,
                    'priority'          => $t['priority'],
                    'status'            => 'pending',
                    'is_custom'         => false,
                    'is_late'           => false,
                    'notes'             => "Auto-generated daily task for day {$day} of {$daysToGenerate}",
                ]);
            }
        }
    }

    /**
     * Evaluate scale_trigger conditions against the event.
     *
     * Examples of valid trigger strings:
     *   "any"              → always true
     *   "capacity > 200"   → true if event capacity > 200
     *   "venue = indoor"   → true if event venue_type is indoor
     *   "duration > 3"     → true if event is longer than 3 days
     */
    protected function passesTrigger($template, Event $event): bool
    {
        $trigger = trim($template->scale_trigger ?? 'any');

        if ($trigger === '' || $trigger === 'any' || $trigger === 'all') {
            return true;
        }

        $eventStart    = Carbon::parse($event->start_date);
        $eventEnd      = Carbon::parse($event->end_date);
        $durationDays  = $eventStart->diffInDays($eventEnd) + 1;

        // Parse "field operator value"
        if (preg_match('/^(\w+)\s*(>|<|>=|<=|=|!=)\s*(.+)$/', $trigger, $m)) {
            [, $field, $op, $value] = $m;
            $value = trim($value);

            $actual = match ($field) {
                'capacity' => (int)($event->capacity ?? 0),
                'duration' => $durationDays,
                'venue'    => strtolower($event->venue_type ?? ''),
                default    => null,
            };

            if ($actual === null) return true;

            return match ($op) {
                '>'  => $actual > (int)$value,
                '<'  => $actual < (int)$value,
                '>=' => $actual >= (int)$value,
                '<=' => $actual <= (int)$value,
                '='  => (string)$actual === strtolower($value),
                '!=' => (string)$actual !== strtolower($value),
                default => true,
            };
        }

        return true;
    }
}
