<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function scannedAttendances()
    {
        return $this->hasMany(AttendanceLog::class, 'scanned_by');
    }

    public function completedTasks()
    {
        return $this->hasMany(EventTask::class, 'completed_by');
    }

    public function recordedContributions()
    {
        return $this->hasMany(EventContribution::class, 'recorded_by');
    }

    public function roles()
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isOrganizer(): bool
    {
        return $this->hasRole('organizer'); // was 'user'
    }
}
