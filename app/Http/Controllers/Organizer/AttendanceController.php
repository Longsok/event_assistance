<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\AttendanceLog;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function __construct(private QrCodeService $qrCodeService) {}

    public function index(Event $event)
    {
        $this->authorizeEvent($event);

        $qrCode = null;
        if ($event->attendance_token) {
            $qrCode = $this->qrCodeService->generateEventQr($event);
        }

        $checkedIn = AttendanceLog::whereHas('eventGuest', fn($q) =>
                         $q->where('event_id', $event->id)
                     )
                     ->with('eventGuest.guest')
                     ->latest('checked_in_at')
                     ->get();

        $stats = [
            'expected'   => $event->eventGuests()->where('rsvp_status', 'confirmed')->count(),
            'checked_in' => $checkedIn->count(),
        ];

        return view('attendance.index', compact('event', 'qrCode', 'checkedIn', 'stats'));
    }

    // Start check-in — generate attendance token and QR
    public function startCheckin(Event $event)
    {
        $this->authorizeEvent($event);

        $event->update([
            'attendance_token' => Str::uuid(),
            'status'           => 'ongoing',
        ]);

        return back()->with('success', 'Check-in started. QR code is now active.');
    }

    // Stop check-in
    public function stopCheckin(Event $event)
    {
        $this->authorizeEvent($event);

        $event->update(['attendance_token' => null]);

        return back()->with('success', 'Check-in stopped.');
    }

    // Manual check-in by organizer
    public function manualCheckin(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'event_guest_id' => 'required|exists:event_guests,id',
        ]);

        $eventGuest = EventGuest::findOrFail($request->event_guest_id);

        // Already checked in
        if ($eventGuest->attendanceLogs()->exists()) {
            return back()->with('error', 'Guest already checked in.');
        }

        AttendanceLog::create([
            'event_guest_id' => $eventGuest->id,
            'scanned_by'     => auth()->id(),
            'scan_method'    => 'manual',
            'checked_in_at'  => now(),
        ]);

        return back()->with('success', "{$eventGuest->guest->name} checked in manually.");
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
