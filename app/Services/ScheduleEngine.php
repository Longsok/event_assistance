<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventSchedule;
use Carbon\Carbon;

class ScheduleEngine
{
    public function generate(Event $event): void
    {
        $templates = $event->category?->scheduleTemplates()
            ->orderBy('sort_order')
            ->get();

        if (!$templates || $templates->isEmpty()) {
            return;
        }

        $eventStart   = Carbon::parse($event->start_date);
        $eventEnd     = Carbon::parse($event->end_date);
        $durationDays = max(1, $eventStart->diffInDays($eventEnd) + 1);

        EventSchedule::where('event_id', $event->id)->delete();

        $capacity     = max(1, (int)($event->capacity ?? 1));
        $mealProvided = (bool)($event->meal_provided ?? true);

        for ($day = 1; $day <= $durationDays; $day++) {
            $date     = $eventStart->copy()->addDays($day - 1);
            $sortBase = ($day - 1) * 100;

            if ($mealProvided) {
                $this->addDayMealSessions($event, $date, $day, $capacity, $sortBase);
            }

            foreach ($templates as $i => $template) {
                $dur       = (int)($template->duration_minutes ?? 60);
                $startTime = $this->resolveTime(
                    $template,
                    $event->start_time ?? '08:00',
                    $event->end_time   ?? '18:00'
                );
                $endTime = $this->addMinutes($startTime, $dur);

                EventSchedule::create([
                    'event_id'         => $event->id,
                    'day_number'       => $day,
                    'schedule_date'    => $date->toDateString(),
                    'session_name'     => $durationDays > 1
                        ? "Day {$day}: {$template->session_name}"
                        : $template->session_name,
                    'start_time'       => $startTime,
                    'end_time'         => $endTime,
                    'duration_minutes' => $dur,
                    'speaker'          => $template->speaker ?? null,
                    'location'         => $template->location ?? null,
                    'sort_order'       => $sortBase + ($template->sort_order ?? $i),
                ]);
            }
        }
    }

    protected function addDayMealSessions(
        Event $event,
        Carbon $date,
        int $day,
        int $capacity,
        int $sortBase
    ): void {
        $meals = [
            ['session_name' => 'Breakfast',   'start' => '07:30', 'duration' => 45,  'sort' => 1],
            ['session_name' => 'Lunch Break', 'start' => '12:00', 'duration' => 60,  'sort' => 40],
            ['session_name' => 'Dinner',      'start' => '18:00', 'duration' => 90,  'sort' => 80],
        ];

        foreach ($meals as $meal) {
            EventSchedule::create([
                'event_id'         => $event->id,
                'day_number'       => $day,
                'schedule_date'    => $date->toDateString(),
                'session_name'     => "Day {$day}: {$meal['session_name']} ({$capacity} guests)",
                'start_time'       => $meal['start'],
                'end_time'         => $this->addMinutes($meal['start'], $meal['duration']),
                'duration_minutes' => $meal['duration'],
                'speaker'          => null,
                'location'         => 'Dining Area',
                'sort_order'       => $sortBase + $meal['sort'],
            ]);
        }
    }

    protected function resolveTime(
        $template,
        string $eventStart,
        string $eventEnd
    ): string {
        $anchor      = $template->anchor ?? 'start';
        $offset      = (int)($template->offset_minutes ?? 0);
        $duration    = (int)($template->duration_minutes ?? 60);
        $positionPct = (int)($template->position_percent ?? 50);

        $startMin = $this->timeToMinutes($eventStart);
        $endMin   = $this->timeToMinutes($eventEnd);
        $dayMin   = max(1, $endMin - $startMin);

        $resolved = match ($anchor) {
            'start'        => $startMin + $offset,
            'end'          => $endMin - $duration - $offset,
            'middle'       => $startMin + (int)($dayMin / 2),
            'proportional' => $startMin + (int)($dayMin * $positionPct / 100),
            default        => $startMin + $offset,
        };

        return $this->minutesToTime(max(0, $resolved));
    }

    protected function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time . ':00');
        return ((int)$h) * 60 + ((int)$m);
    }

    protected function minutesToTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60) % 24, $minutes % 60);
    }

    protected function addMinutes(string $time, int $add): string
    {
        return $this->minutesToTime($this->timeToMinutes($time) + $add);
    }
}
