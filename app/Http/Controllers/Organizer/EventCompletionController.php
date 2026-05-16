<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCompletion;
use Illuminate\Http\Request;

class EventCompletionController extends Controller
{
    public function show(Event $event)
    {
        $this->authorizeEvent($event);

        $completion = $event->completion;

        return view('events.complete', compact('event', 'completion'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'organizer_notes' => 'nullable|string',
        ]);

        // Build snapshot from live data
        $totalInvited   = $event->eventGuests()->count();
        $totalAttended  = $event->eventGuests()
            ->whereHas('attendanceLogs')
            ->count();
        $tasksTotal     = $event->tasks()->count();
        $tasksCompleted = $event->tasks()->where('status', 'done')->count();
        $totalBudget    = $event->budget?->total_budget ?? 0;
        $totalSpent     = $event->budget?->items->sum('actual_amount') ?? 0;
        $totalContribs  = $event->contributions()
            ->where('status', 'received')
            ->sum('amount');

        EventCompletion::updateOrCreate(
            ['event_id' => $event->id],
            [
                'total_invited'       => $totalInvited,
                'total_attended'      => $totalAttended,
                'tasks_completed'     => $tasksCompleted,
                'tasks_total'         => $tasksTotal,
                'total_budget'        => $totalBudget,
                'total_spent'         => $totalSpent,
                'total_contributions' => $totalContribs,
                'organizer_notes'     => $request->organizer_notes,
                'completed_at'        => now(),
            ]
        );

        // Mark event as completed
        $event->update(['status' => 'completed']);

        return redirect()->route('events.completion.show', $event)
                         ->with('success', 'Event marked as completed.');
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
