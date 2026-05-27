<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Models\EventGuest;
use Illuminate\Http\Request;

class GuestRegistrationController extends Controller
{
    public function show(string $inviteToken)
    {
        $event = Event::where('invite_token', $inviteToken)
            ->where('allow_self_registration', true)
            ->whereIn('status', ['draft', 'published', 'ongoing'])
            ->with(['inviteCard', 'schedules', 'category'])
            ->firstOrFail();

        if ($event->max_registrations) {
            $registered = $event->eventGuests()->count();
            if ($registered >= $event->max_registrations) {
                return view('public.register', ['event' => $event, 'isFull' => true]);
            }
        }

        return view('public.register', compact('event'));
    }

    public function store(Request $request, string $inviteToken)
    {
        $event = Event::where('invite_token', $inviteToken)
            ->where('allow_self_registration', true)
            ->whereIn('status', ['draft', 'published', 'ongoing'])
            ->with(['inviteCard', 'schedules', 'category'])
            ->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // At least one contact detail required
        if (!$request->filled('email') && !$request->filled('phone')) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Please provide at least an email or phone number.']);
        }

        // ── Guest lookup ───────────────────────────────────────────
        // Only match an existing guest if an email was provided AND
        // that email already exists under this organiser.
        // Never match by user_id alone — that would silently reuse
        // the first guest in the list when no email is given.
        $guest = null;

        if ($request->filled('email')) {
            $guest = Guest::where('user_id', $event->user_id)
                ->where('email', $request->email)
                ->first();
        }

        if (!$guest) {
            $guest = Guest::create([
                'user_id' => $event->user_id,
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
            ]);
        }

        // Schedule for today (used in both registered + alreadyJoined states)
        $todaySchedule = $event->schedules
            ->filter(fn($s) => optional($s->schedule_date)->isToday())
            ->sortBy('start_time')
            ->values();

        $alreadyRegistered = EventGuest::where('event_id', $event->id)
            ->where('guest_id', $guest->id)
            ->exists();

        if ($alreadyRegistered) {
            $eventGuest = EventGuest::where('event_id', $event->id)
                ->where('guest_id', $guest->id)
                ->first();

            return view('public.register', [
                'event'         => $event,
                'alreadyJoined' => true,
                'guestCode'     => $eventGuest->guest_code,
                'guestName'     => $guest->name,
                'todaySchedule' => $todaySchedule,
            ]);
        }

        $eventGuest = EventGuest::create([
            'event_id'       => $event->id,
            'guest_id'       => $guest->id,
            'rsvp_status'    => 'confirmed',
            'registered_via' => 'invite_link',
            'registered_at'  => now(),
        ]);

        return view('public.register', [
            'event'         => $event,
            'registered'    => true,
            'guestCode'     => $eventGuest->guest_code,
            'guestName'     => $guest->name,
            'todaySchedule' => $todaySchedule,
        ]);
    }
}
