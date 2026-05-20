<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    // Show the check-in page
    public function show(string $attendanceToken)
    {
        $event = Event::where('attendance_token', $attendanceToken)
            ->where('status', 'ongoing')
            ->firstOrFail();

        return view('public.checkin', compact('event'));
    }

    // Handle check-in form submission
    public function store(Request $request, string $attendanceToken)
    {
        $event = Event::where('attendance_token', $attendanceToken)
            ->where('status', 'ongoing')
            ->firstOrFail();

        $request->validate([
            'guest_code' => 'required|string',
            'name'       => 'required|string|max:255',
        ]);

        // Find event guest by guest_code
        $eventGuest = EventGuest::where('event_id', $event->id)
            ->where('guest_code', strtoupper(trim($request->guest_code)))
            ->first();

        // Guest code not found
        if (!$eventGuest) {
            return back()
                ->withInput()
                ->withErrors(['guest_code' => 'Guest ID not found for this event.']);
        }

        // Fuzzy name check — case insensitive, partial match
        $inputName  = strtolower(trim($request->name));
        $guestName  = strtolower($eventGuest->guest->name);

        $nameMatches = str_contains($guestName, $inputName)
            || str_contains($inputName, $guestName)
            || similar_text($inputName, $guestName) >= (strlen($guestName) * 0.7);

        if (!$nameMatches) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Name does not match our records. Please try again.']);
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

        // Record attendance
        AttendanceLog::create([
            'event_guest_id' => $eventGuest->id,
            'scanned_by'     => null, // self check-in
            'scan_method'    => 'self',
            'checked_in_at'  => now(),
        ]);

        // Update RSVP to attended
        $eventGuest->update(['rsvp_status' => 'attended']);

        // Get today's schedule to show on success page
        $todaySchedule = $event->schedules()
            ->where('schedule_date', today())
            ->orderBy('start_time')
            ->get();

        return view('public.checkin-success', [
            'event'         => $event,
            'guestName'     => $eventGuest->guest->name,
            'checkedInAt'   => now(),
            'todaySchedule' => $todaySchedule, // show agenda after check-in
        ]);
    }
}
