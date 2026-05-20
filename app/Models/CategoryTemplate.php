<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTemplate extends Model
{
    protected $table = 'category_tamplates';
    
    protected $fillable = [
        'category_id',
        'group_id',
        'task_name',
        'days_before',
        'anchor',
        'offset_days',
        'position_percent',
        'priority',
        'scale_trigger',
        'notes',
        'sort_order',
    ];



    protected $casts = [
        'position_percent' => 'decimal:2',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function group()
    {
        return $this->belongsTo(TaskGroup::class, 'group_id');
    }
}
