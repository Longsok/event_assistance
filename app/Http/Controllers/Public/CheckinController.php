<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function show(string $attendanceToken)
    {
        $event = Event::where('attendance_token', $attendanceToken)->first();

        // Unknown token -> friendly page, not a raw 404.
        if (!$event) {
            return response()->view('public.checkin', [
                'event'         => null,
                'checkinClosed' => true,
                'closedReason'  => 'This check-in link is not valid.',
            ], 404);
        }

        // Token is valid but check-in is not currently open for this event.
        if (!in_array($event->status, ['published', 'ongoing'], true)) {
            return view('public.checkin', [
                'event'         => $event,
                'checkinClosed' => true,
                'closedReason'  => 'Check-in for this event is not open right now.',
            ]);
        }

        return view('public.checkin', compact('event'));
    }

    // Handle check-in form submission
   public function store(Request $request, string $attendanceToken)
    {
        $event = Event::where('attendance_token', $attendanceToken)->first();

        if (!$event || !in_array($event->status, ['published', 'ongoing'], true)) {
            return view('public.checkin', [
                'event'         => $event,
                'checkinClosed' => true,
                'closedReason'  => 'Check-in for this event is not open right now.',
            ]);
        }
        
        $request->validate([
            'guest_code' => 'required|string',
            'name'       => 'required|string|max:255',
        ]);

        $eventGuest = EventGuest::where('event_id', $event->id)
            ->where('guest_code', strtoupper(trim($request->guest_code)))
            ->first();

        if (!$eventGuest) {
            return back()->withInput()
                ->withErrors(['guest_code' => 'Guest code not found for this event.']);
        }

        // Fuzzy name check
        $inputName  = strtolower(trim($request->name));
        $guestName  = strtolower($eventGuest->guest->name);

        $nameMatches = str_contains($guestName, $inputName)
            || str_contains($inputName, $guestName)
            || similar_text($inputName, $guestName) >= (strlen($guestName) * 0.7);

        if (!$nameMatches) {
            return back()->withInput()
                ->withErrors(['name' => 'Name does not match. Please try again.']);
        }

        // Already checked in
        if ($eventGuest->attendanceLogs()->exists()) {
            $checkedInAt = $eventGuest->attendanceLogs()->first()->checked_in_at;
            return view('public.checkin', [
                'event'           => $event,
                'alreadyCheckedIn'=> true,
                'guestName'       => $eventGuest->guest->name,
                'checkedInAt'     => $checkedInAt,
            ]);
        }

        AttendanceLog::create([
            'event_guest_id' => $eventGuest->id,
            'scanned_by'     => null,
            'scan_method'    => 'self',
            'checked_in_at'  => now(),
        ]);

        $eventGuest->update(['rsvp_status' => 'attended']);

        $todaySchedule = $event->schedules()
            ->where('schedule_date', today())
            ->orderBy('start_time')
            ->get();

        return view('public.checkin-success', [
            'event'         => $event,
            'guestName'     => $eventGuest->guest->name,
            'checkedInAt'   => now(),
            'todaySchedule' => $todaySchedule,
        ]);
    }
}
