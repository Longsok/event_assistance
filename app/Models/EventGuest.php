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
        'invited_at'         => 'datetime',
        'registered_at'      => 'datetime',
        'confirmation_sent'  => 'boolean',
    ];

    // Auto generate guest_code on creation
    protected static function booted(): void
    {
        static::creating(function (EventGuest $eventGuest) {
            if (empty($eventGuest->guest_code)) {
                $prefix = strtoupper(substr($eventGuest->event->category->slug ?? 'EVT', 0, 4));
                $year   = now()->year;
                $count  = static::where('event_id', $eventGuest->event_id)->count() + 1;
                $eventGuest->guest_code = "{$prefix}-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
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
