<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventGuest extends Model
{
    protected $fillable = [
        'event_id',
        'guest_id',
        'guest_code',
        'rsvp_status',
        'registered_via',
        'seat_number',
        'meal_preference',
        'invited_at',
        'registered_at',
        'confirmation_sent',
    ];

    protected $casts = [
        'invited_at'        => 'datetime',
        'registered_at'     => 'datetime',
        'confirmation_sent' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (EventGuest $eventGuest) {
            if (empty($eventGuest->guest_code)) {
                $prefix = strtoupper(substr($eventGuest->event->category->slug ?? 'EVT', 0, 4));
                $year   = now()->year;
                $base   = "{$prefix}-{$year}-";

                // Use DB::table() to bypass model scopes and find the
                // highest existing code for this prefix globally
                $maxCode = \DB::table('event_guests')
                    ->where('guest_code', 'like', $base . '%')
                    ->max('guest_code');

                if ($maxCode) {
                    $lastNum = (int) substr($maxCode, strrpos($maxCode, '-') + 1);
                    $next = $lastNum + 1;
                } else {
                    $next = 1;
                }

                $eventGuest->guest_code = $base . str_pad($next, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isCheckedIn(): bool
    {
        return $this->attendanceLogs()->exists();
    }

    // ─── Relationships ───────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
