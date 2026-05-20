<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eventGuests()
    {
        return $this->hasMany(EventGuest::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_guests')
                    ->withPivot('guest_code', 'rsvp_status', 'registered_via')
                    ->withTimestamps();
    }

    public function contributions()
    {
        return $this->hasMany(EventContribution::class);
    }
}
