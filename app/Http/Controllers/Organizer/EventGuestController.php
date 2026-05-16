<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Models\EventGuest;
use Illuminate\Http\Request;

class EventGuestController extends Controller
{
    // Guest list for a specific event
    public function index(Event $event)
    {
        $this->authorizeEvent($event);

        $eventGuests = $event->eventGuests() ->with('guest') ->latest() ->paginate(20);

        $stats = [
            'total'     => $event->eventGuests()->count(),
            'confirmed' => $event->eventGuests()->where('rsvp_status', 'confirmed')->count(),
            'pending'   => $event->eventGuests()->where('rsvp_status', 'pending')->count(),
            'declined'  => $event->eventGuests()->where('rsvp_status', 'declined')->count(),
        ];

        // Available guests not yet added to this event
        $availableGuests = Guest::where('user_id', auth()->id())
            ->whereNotIn('id', $event->eventGuests()->pluck('guest_id')) ->get();

        return view('guests.event-guests', compact(
            'event',
            'eventGuests',
            'stats',
            'availableGuests'
        ));
    }

    // Attach existing guest to event
    public function store(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'guest_id' => 'required|exists:guests,id',
        ]);

        // Prevent duplicate
        if ($event->eventGuests()->where('guest_id', $request->guest_id)->exists()) {
            return back()->with('error', 'Guest already added to this event.');
        }

        EventGuest::create([
            'event_id'    => $event->id,
            'guest_id'    => $request->guest_id,
            'invited_at'  => now(),
        ]);

        return back()->with('success', 'Guest added to event.');
    }

    // Update RSVP status, seat, meal preference
    public function update(Request $request, Event $event, EventGuest $eventGuest)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'rsvp_status'     => 'required|in:pending,confirmed,declined',
            'seat_number'     => 'nullable|string|max:50',
            'meal_preference' => 'nullable|string|max:100',
        ]);

        $eventGuest->update($request->only([
            'rsvp_status',
            'seat_number',
            'meal_preference',
        ]));

        return back()->with('success', 'Guest updated.');
    }

    public function destroy(Event $event, EventGuest $eventGuest)
    {
        $this->authorizeEvent($event);
        $eventGuest->delete();
        return back()->with('success', 'Guest removed from event.');
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
