<?php

namespace App\Models;

use App\Models\CategoryTemplate;
use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
    ];

    // ─── Relationships ───────────────────────────────────────

    // Category templates belonging to this group
    public function categoryTemplates()
    {
        return $this->hasMany(CategoryTemplate::class, 'group_id');
    }

    // Event tasks belonging to this group
    public function eventTasks()
    {
        return $this->hasMany(EventTask::class, 'group_id');
    }
}
