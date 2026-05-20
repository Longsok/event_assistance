<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventSchedule;
use Illuminate\Http\Request;

class EventScheduleController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizeEvent($event);

        $schedulesByDay = $event->schedules() ->orderBy('day_number') ->orderBy('start_time')
            ->get()
            ->groupBy('day_number');

        return view('schedules.index', compact('event', 'schedulesByDay'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'session_name'     => 'required|string|max:255',
            'day_number'       => 'required|integer|min:1',
            'start_time'       => 'required|string',
            'end_time'         => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'location'         => 'nullable|string|max:255',
            'is_break'         => 'boolean',
            'notes'            => 'nullable|string',
        ]);

        $scheduleDate = $event->start_date->copy()
            ->addDays($request->day_number - 1);

        EventSchedule::create([
            'event_id'         => $event->id,
            'day_number'       => $request->day_number,
            'schedule_date'    => $scheduleDate,
            'session_name'     => $request->session_name,
            'start_time'       => $request->start_time,
            'end_time'         => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'location'         => $request->location,
            'is_break'         => $request->boolean('is_break'),
            'is_custom'        => true,
            'notes'            => $request->notes,
            'sort_order'       => EventSchedule::where('event_id', $event->id)
                                               ->where('day_number', $request->day_number)
                                               ->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Session added.');
    }

    public function update(Request $request, Event $event, EventSchedule $schedule)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'session_name'     => 'required|string|max:255',
            'start_time'       => 'required|string',
            'end_time'         => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'location'         => 'nullable|string|max:255',
        ]);

        $schedule->update($request->only([
            'session_name',
            'start_time',
            'end_time',
            'duration_minutes',
            'location',
            'is_break',
            'notes',
        ]));

        return back()->with('success', 'Session updated.');
    }

    public function destroy(Event $event, EventSchedule $schedule)
    {
        $this->authorizeEvent($event);
        $schedule->delete();
        return back()->with('success', 'Session deleted.');
    }

    public function reorder(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        foreach ($request->order as $position => $id) {
            EventSchedule::where('id', $id)
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
