<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class AttendanceScanController extends Controller
{
    // Organizer QR scanner page
    public function scan(Event $event)
    {
        abort_if($event->user_id !== auth()->id(), 403);

        return view('attendance.scan', compact('event'));
    }

    // AJAX: process scanned QR code
    public function process(Request $request, Event $event)
    {
        abort_if($event->user_id !== auth()->id(), 403);

        $request->validate([
            'guest_code' => 'required|string',
        ]);

        $code       = strtoupper(trim($request->guest_code));
        $eventGuest = EventGuest::where('event_id', $event->id)
            ->where('guest_code', $code)
            ->with('guest')
            ->first();

        if (!$eventGuest) {
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Guest code not found for this event.',
            ], 404);
        }

        // Already checked in
        if ($eventGuest->attendanceLogs()->exists()) {
            $log = $eventGuest->attendanceLogs()->first();
            return response()->json([
                'status'       => 'already_checked_in',
                'message'      => 'Already checked in',
                'guest_name'   => $eventGuest->guest->name,
                'checked_in_at'=> $log->checked_in_at->format('h:i A'),
                'rsvp_status'  => $eventGuest->rsvp_status,
            ]);
        }

        // Check in
        AttendanceLog::create([
            'event_guest_id' => $eventGuest->id,
            'scanned_by'     => auth()->id(),
            'scan_method'    => 'qr_scan',
            'checked_in_at'  => now(),
        ]);

        $eventGuest->update(['rsvp_status' => 'attended']);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Checked in successfully!',
            'guest_name'  => $eventGuest->guest->name,
            'guest_code'  => $code,
            'rsvp_status' => 'attended',
            'checked_in_at' => now()->format('h:i A'),
        ]);
    }

    // AJAX: get live check-in count
    public function stats(Event $event)
    {
        abort_if($event->user_id !== auth()->id(), 403);

        $total     = $event->eventGuests()->count();
        $checkedIn = AttendanceLog::whereHas('eventGuest', fn($q) =>
            $q->where('event_id', $event->id)
        )->count();

        return response()->json([
            'total'      => $total,
            'checked_in' => $checkedIn,
            'remaining'  => $total - $checkedIn,
        ]);
    }
}
