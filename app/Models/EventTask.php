<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;        // ✅ add this
use Illuminate\Database\Eloquent\SoftDeletes;
=======
use Illuminate\Database\Eloquent\Model;
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882

class EventTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'group_id',
        'task_name',
        'original_due_date',
        'due_date',
        'priority',
        'status',
        'is_custom',
        'is_late',
        'late_note',
        'completed_by',
        'completed_at',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'due_date'          => 'date',
        'original_due_date' => 'date',
        'completed_at'      => 'datetime',
        'is_custom'         => 'boolean',
        'is_late'           => 'boolean',
    ];

<<<<<<< HEAD
=======
    // ─── Helpers ─────────────────────────────────────────────

>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || ($this->due_date->isPast() && !$this->isDone());
    }

<<<<<<< HEAD
=======
    // ─── Relationships ───────────────────────────────────────

>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function group()
    {
        return $this->belongsTo(TaskGroup::class, 'group_id');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
