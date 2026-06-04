<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
    'user_id',
    'category_id',
    'title',
    'slug',
    'description',
    'venue',
    'address',
    'province',
    'start_date',
    'end_date',
    'start_time',
    'end_time',
    'capacity',
    'venue_type',
    'meal_provided',
    'status',
    'is_public',
    'cover_image',
    'invite_token',
    'attendance_token',
    'allow_self_registration',
    'max_registrations',
];

    protected $casts = [
        'start_date'              => 'date',
        'end_date'                => 'date',
        'meal_provided'           => 'boolean',
        'is_public'               => 'boolean',
        'allow_self_registration' => 'boolean',
    ];

    // Auto generate slug and invite_token on creation
    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            $event->slug = Str::slug($event->title) . '-' . Str::random(6);

            if (empty($event->invite_token)) {
                $event->invite_token = (string) Str::uuid();
            }

            // Give every event a stable attendance token up front so the
            // check-in QR can always be generated. Whether check-in is
            // actually open is governed by the event status, not by the
            // presence of this token.
            if (empty($event->attendance_token)) {
                $event->attendance_token = (string) Str::uuid();
            }
        });
    }
    // ─── Helpers ─────────────────────────────────────────────

    public function getTotalDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isOngoing(): bool
    {
        return $this->status === 'ongoing';
    }

    // ─── Relationships ───────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function eventGuests()
    {
        return $this->hasMany(EventGuest::class);
    }

    public function guests()
    {
        return $this->belongsToMany(Guest::class, 'event_guests')
                    ->withPivot('guest_code', 'rsvp_status', 'registered_via')
                    ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(EventTask::class)->orderBy('due_date');
    }

    public function schedules()
    {
        return $this->hasMany(EventSchedule::class)->orderBy('schedule_date')->orderBy('start_time');
    }

    public function budget()
    {
        return $this->hasOne(EventBudget::class);
    }

    public function contributions()
    {
        return $this->hasMany(EventContribution::class);
    }

    public function completion()
    {
        return $this->hasOne(EventCompletion::class);
    }

    public function inviteCard()
    {
        return $this->hasOne(InviteCard::class);
    }
}
