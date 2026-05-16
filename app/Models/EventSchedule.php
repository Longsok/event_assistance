<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    protected $fillable = [
        'event_id',
        'day_number',
        'schedule_date',
        'session_name',
        'start_time',
        'end_time',
        'duration_minutes',
        'location',
        'is_break',
        'is_custom',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'is_break'      => 'boolean',
        'is_custom'     => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
