<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCompletion extends Model
{
     protected $fillable = [
        'event_id',
        'total_invited',
        'total_attended',
        'tasks_completed',
        'tasks_total',
        'total_budget',
        'total_spent',
        'total_contributions',
        'organizer_notes',
        'completed_at',
    ];

    protected $casts = [
        'total_budget'        => 'decimal:2',
        'total_spent'         => 'decimal:2',
        'total_contributions' => 'decimal:2',
        'completed_at'        => 'datetime',
    ];

    // ─── Helpers ─────────────────────────────────────────────

    public function getAttendanceRateAttribute(): float
    {
        if ($this->total_invited === 0) return 0;
        return round(($this->total_attended / $this->total_invited) * 100, 1);
    }

    public function getTaskCompletionRateAttribute(): float
    {
        if ($this->tasks_total === 0) return 0;
        return round(($this->tasks_completed / $this->tasks_total) * 100, 1);
    }

    // ─── Relationships ───────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
