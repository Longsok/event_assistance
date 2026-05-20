<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventContribution extends Model
{
    protected $fillable = [
        'event_id',
        'guest_id',
        'amount',
        'payment_method',
        'reference_number',
        'status',
        'notes',
        'recorded_by',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
