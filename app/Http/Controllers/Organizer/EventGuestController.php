<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Models\EventGuest;
use Illuminate\Http\Request;

class EventGuestController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizeEvent($event);

        $eventGuests = $event->eventGuests()->with('guest')->latest()->paginate(20);

        $stats = [
            'total'     => $event->eventGuests()->count(),
            'confirmed' => $event->eventGuests()->where('rsvp_status', 'confirmed')->count(),
            'pending'   => $event->eventGuests()->where('rsvp_status', 'pending')->count(),
            'declined'  => $event->eventGuests()->where('rsvp_status', 'declined')->count(),
        ];

        $availableGuests = Guest::where('user_id', auth()->id())
            ->whereNotIn('id', $event->eventGuests()->pluck('guest_id'))
            ->get();

        return view('guests.event-guests', compact('event', 'eventGuests', 'stats', 'availableGuests'));
    }

    // Add guest inline from event show page (name + email/phone)
    public function store(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        // If guest_id provided — attach existing guest from guest book
        if ($request->filled('guest_id')) {
            $request->validate(['guest_id' => 'required|exists:guests,id']);

            if ($event->eventGuests()->where('guest_id', $request->guest_id)->exists()) {
                return back()->with('error', 'Guest already added to this event.');
            }

            EventGuest::create([
                'event_id'   => $event->id,
                'guest_id'   => $request->guest_id,
                'invited_at' => now(),
            ]);

            return back()->with('success', 'Guest added to event.');
        }

        // Otherwise create new guest from inline form
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Require at least email or phone
        if (!$request->filled('email') && !$request->filled('phone')) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Please provide at least an email or phone number.']);
        }

        // Find existing guest by email (avoid duplicates)
        $guest = null;
        if ($request->filled('email')) {
            $guest = Guest::where('user_id', auth()->id())
                ->where('email', $request->email)
                ->first();
        }

        if (!$guest) {
            $guest = Guest::create([
                'user_id' => auth()->id(),
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
            ]);
        }

        // Prevent duplicate event registration
        if ($event->eventGuests()->where('guest_id', $guest->id)->exists()) {
            return back()->with('error', "{$guest->name} is already on the guest list.");
        }

        EventGuest::create([
            'event_id'   => $event->id,
            'guest_id'   => $guest->id,
            'invited_at' => now(),
        ]);

        return back()->with('success', "{$guest->name} added to event.");
    }

    public function update(Request $request, Event $event, EventGuest $eventGuest)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'rsvp_status'     => 'required|in:pending,confirmed,declined',
            'seat_number'     => 'nullable|string|max:50',
            'meal_preference' => 'nullable|string|max:100',
        ]);

        $eventGuest->update($request->only(['rsvp_status', 'seat_number', 'meal_preference']));

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
