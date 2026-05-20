<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleTemplate extends Model
{
    protected $table = 'schedule_tamplates';

    protected $fillable = [
        'category_id',
        'session_name',
        'anchor',
        'offset_minutes',
        'duration_minutes',
        'is_break',
        'scale_trigger',
        'sort_order',
    ];

    protected $casts = [
        'is_break' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }
}
