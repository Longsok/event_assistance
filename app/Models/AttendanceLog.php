<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'event_guest_id',
        'scanned_by',
        'scan_method',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function eventGuest()
    {
        return $this->belongsTo(EventGuest::class);
    }

    // The user who manually scanned (null if guest scanned themselves)
    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
