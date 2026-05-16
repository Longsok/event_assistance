<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Models\EventGuest;
use App\Mail\GuestRegistrationConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GuestRegistrationController extends Controller
{
    // Show the public registration page
    public function show(string $inviteToken)
    {
        $event = Event::where('invite_token', $inviteToken)
            ->where('allow_self_registration', true)
            ->whereIn('status', ['published', 'ongoing'])
            ->firstOrFail();

        // Check if registration is full
        if ($event->max_registrations) {
            $registered = $event->eventGuests()->count();
            if ($registered >= $event->max_registrations) {
                return view('public.register', [
                    'event'    => $event,
                    'isFull'   => true,
                    'schedule' => [],
                ]);
            }
        }

        $schedule = $event->schedules()
            ->orderBy('day_number')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_number');

        return view('public.register', compact('event', 'schedule'));
    }

    // Handle registration form submission
    public function store(Request $request, string $inviteToken)
    {
        $event = Event::where('invite_token', $inviteToken)
            ->where('allow_self_registration', true)
            ->whereIn('status', ['published', 'ongoing'])
            ->firstOrFail();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Find existing guest by email or create new one
        $guest = Guest::where('user_id', $event->user_id)
            ->when($request->email, fn($q) => $q->where('email', $request->email))
            ->first();

        if (!$guest) {
            $guest = Guest::create([
                'user_id' => $event->user_id,
                'name'    => $request->name,
                'email'   => $request->email,
                'phone'   => $request->phone,
            ]);
        }

        // Prevent duplicate registration
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
            ]);
        }

        // Create event_guest record
        $eventGuest = EventGuest::create([
            'event_id'       => $event->id,
            'guest_id'       => $guest->id,
            'rsvp_status'    => 'confirmed',
            'registered_via' => 'invite_link',
            'registered_at'  => now(),
        ]);

        // Send confirmation email if email provided
        if ($guest->email) {
            Mail::to($guest->email)
                ->send(new GuestRegistrationConfirmation($event, $guest, $eventGuest));

            $eventGuest->update(['confirmation_sent' => true]);
        }

        // Get today's schedule to show after registration
        $todaySchedule = $event->schedules()
            ->where('schedule_date', today())
            ->orderBy('start_time')
            ->get();

        return view('public.register', [
            'event'         => $event,
            'registered'    => true,
            'guestCode'     => $eventGuest->guest_code,
            'guestName'     => $guest->name,
            'todaySchedule' => $todaySchedule,
        ]);
    }
}
