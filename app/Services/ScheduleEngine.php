<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\ScheduleTemplate;
use Carbon\Carbon;

class ScheduleEngine
{
    /**
     * Generate schedule sessions for a newly created event.
     */
    public function generate(Event $event): void
    {
        $templates = ScheduleTemplate::where('category_id', $event->category_id)
            ->orderBy('sort_order')
            ->get();

        $totalDays    = $event->total_days;
        $startTime    = $event->start_time
            ? Carbon::parse($event->start_time)
            : Carbon::parse('09:00');
        $endTime      = $event->end_time
            ? Carbon::parse($event->end_time)
            : Carbon::parse('18:00');
        $totalMinutes = $startTime->diffInMinutes($endTime);

        foreach ($templates as $template) {
            if (!$this->passesRules($template->scale_trigger, $event)) {
                continue;
            }

            // Resolve which day(s) this session belongs to
            $dayAssignments = $this->resolveDays($template, $totalDays);

            foreach ($dayAssignments as $dayNumber) {
                $scheduleDate = $event->start_date->copy()->addDays($dayNumber - 1);

                [$sessionStart, $sessionEnd] = $this->resolveTime(
                    $template,
                    $startTime,
                    $endTime,
                    $totalMinutes,
                    $dayNumber,
                    $totalDays
                );

                EventSchedule::create([
                    'event_id'         => $event->id,
                    'day_number'       => $dayNumber,
                    'schedule_date'    => $scheduleDate,
                    'session_name'     => $template->session_name,
                    'start_time'       => $sessionStart->format('H:i'),
                    'end_time'         => $sessionEnd->format('H:i'),
                    'duration_minutes' => $template->duration_minutes,
                    'is_break'         => $template->is_break,
                    'is_custom'        => false,
                    'sort_order'       => $template->sort_order,
                ]);
            }
        }
    }

    /**
     * Recalculate schedules when event dates change.
     * Only recalculates system-generated sessions.
     */
    public function recalculate(Event $event): void
    {
        // Remove old system-generated schedules
        EventSchedule::where('event_id', $event->id)
            ->where('is_custom', false)
            ->delete();

        // Regenerate from templates
        $this->generate($event);
    }

    /**
     * Resolve which day numbers a session applies to.
     */
    private function resolveDays(ScheduleTemplate $template, int $totalDays): array
    {
        return match ($template->anchor) {
            'start'        => [1],
            'end'          => [$totalDays],
            'middle'       => [(int) ceil($totalDays / 2)],
            'proportional' => $this->distributeAcrossDays($template, $totalDays),
            default        => [1],
        };
    }

    /**
     * Distribute proportional sessions across all days.
     */
    private function distributeAcrossDays(ScheduleTemplate $template, int $totalDays): array
    {
        if ($totalDays === 1) {
            return [1];
        }

        // For multi-day events distribute middle sessions
        // Skip day 1 (opening) and last day (closing) for proportional
        $middleDays = range(2, $totalDays - 1);

        if (empty($middleDays)) {
            return [1];
        }

        // Assign one session per middle day
        return $middleDays;
    }

    /**
     * Resolve start and end time for a session.
     */
    private function resolveTime(
        ScheduleTemplate $template,
        Carbon $dayStart,
        Carbon $dayEnd,
        int $totalMinutes,
        int $dayNumber,
        int $totalDays
    ): array {
        $sessionStart = match ($template->anchor) {
            'start' =>
                $dayStart->copy()->addMinutes($template->offset_minutes),

            'end' =>
                $dayEnd->copy()
                    ->subMinutes($template->duration_minutes)
                    ->addMinutes($template->offset_minutes),

            'middle' =>
                $dayStart->copy()
                    ->addMinutes((int) ($totalMinutes / 2))
                    ->addMinutes($template->offset_minutes),

            'proportional' =>
                $dayStart->copy()
                    ->addMinutes($template->offset_minutes),

            default => $dayStart->copy(),
        };

        $sessionEnd = $sessionStart->copy()->addMinutes($template->duration_minutes);

        return [$sessionStart, $sessionEnd];
    }

    /**
     * Evaluate scale_trigger rules — same logic as TimelineEngine.
     */
    private function passesRules(string $trigger, Event $event): bool
    {
        if ($trigger === 'any' || empty($trigger)) {
            return true;
        }

        if (preg_match('/capacity\s*>\s*(\d+)/', $trigger, $m)) {
            return $event->capacity > (int) $m[1];
        }
        if (preg_match('/capacity\s*<=\s*(\d+)/', $trigger, $m)) {
            return $event->capacity <= (int) $m[1];
        }
        if (preg_match('/venue\s*=\s*(\w+)/', $trigger, $m)) {
            return $event->venue_type === $m[1];
        }
        if (preg_match('/meal\s*=\s*(yes|no)/', $trigger, $m)) {
            return $event->meal_provided === ($m[1] === 'yes');
        }
        if (preg_match('/days\s*>\s*(\d+)/', $trigger, $m)) {
            return $event->total_days > (int) $m[1];
        }

        return true;
    }
}
