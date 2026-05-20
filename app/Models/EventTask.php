<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;        // ✅ add this
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || ($this->due_date->isPast() && !$this->isDone());
    }

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
