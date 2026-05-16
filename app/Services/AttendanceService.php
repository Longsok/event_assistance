<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\AttendanceLog;

class AttendanceService
{
    /**
     * Validate and process a guest check-in.
     * Returns result array with status and message.
     */
    public function checkin(Event $event, string $guestCode, string $name): array
    {
        // Step 1 — Find event guest by guest code
        $eventGuest = EventGuest::where('event_id', $event->id)
            ->where('guest_code', strtoupper(trim($guestCode)))
            ->with('guest')
            ->first();

        if (!$eventGuest) {
            return [
                'success' => false,
                'status'  => 'not_found',
                'message' => 'Guest ID not found for this event. Please check your ID and try again.',
            ];
        }

        // Step 2 — Verify name matches
        if (!$this->nameMatches($name, $eventGuest->guest->name)) {
            return [
                'success' => false,
                'status'  => 'name_mismatch',
                'message' => 'Name does not match our records. Please check your name and try again.',
            ];
        }

        // Step 3 — Check if already checked in
        if ($eventGuest->attendanceLogs()->exists()) {
            $log = $eventGuest->attendanceLogs()->first();
            return [
                'success'      => false,
                'status'       => 'already_checked_in',
                'message'      => 'You are already checked in!',
                'checked_in_at'=> $log->checked_in_at,
                'guest_name'   => $eventGuest->guest->name,
            ];
        }

        // Step 4 — Record attendance
        AttendanceLog::create([
            'event_guest_id' => $eventGuest->id,
            'scanned_by'     => null,
            'scan_method'    => 'self',
            'checked_in_at'  => now(),
        ]);

        // Step 5 — Update RSVP status to attended
        $eventGuest->update(['rsvp_status' => 'attended']);

        return [
            'success'        => true,
            'status'         => 'checked_in',
            'message'        => "Welcome, {$eventGuest->guest->name}!",
            'guest_name'     => $eventGuest->guest->name,
            'checked_in_at'  => now(),
        ];
    }

    /**
     * Get live attendance stats for an event.
     * Used by Livewire AttendanceCounter component.
     */
    public function getStats(Event $event): array
    {
        $total      = $event->eventGuests()->count();
        $confirmed  = $event->eventGuests()->where('rsvp_status', 'confirmed')->count()
                    + $event->eventGuests()->where('rsvp_status', 'attended')->count();
        $checkedIn  = AttendanceLog::whereHas('eventGuest',
                          fn($q) => $q->where('event_id', $event->id)
                      )->count();
        $pending    = $confirmed - $checkedIn;
        $rate       = $confirmed > 0
                      ? round(($checkedIn / $confirmed) * 100, 1)
                      : 0;

        return [
            'total'      => $total,
            'confirmed'  => $confirmed,
            'checked_in' => $checkedIn,
            'pending'    => max(0, $pending),
            'rate'       => $rate,
        ];
    }

    /**
     * Fuzzy name matching.
     * Handles: case differences, partial names, typos.
     *
     * "panha meas"  vs "Panha Meas"   → true (case)
     * "panha"       vs "Panha Meas"   → true (partial)
     * "panha meas"  vs "panha"        → true (partial)
     * "panha maes"  vs "Panha Meas"   → true (70% match)
     * "john"        vs "Panha Meas"   → false
     */
    private function nameMatches(string $input, string $stored): bool
    {
        $input  = strtolower(trim($input));
        $stored = strtolower(trim($stored));

        // Exact match
        if ($input === $stored) return true;

        // Input is contained in stored name
        if (str_contains($stored, $input)) return true;

        // Stored name is contained in input
        if (str_contains($input, $stored)) return true;

        // First name only match
        $storedFirstName = explode(' ', $stored)[0];
        $inputFirstName  = explode(' ', $input)[0];
        if ($inputFirstName === $storedFirstName) return true;

        // Fuzzy match — at least 70% similarity
        similar_text($input, $stored, $percent);
        if ($percent >= 70) return true;

        return false;
    }
}
