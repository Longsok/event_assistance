<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
    ];

    public function categoryTemplates()
    {
        return $this->hasMany(CategoryTemplate::class, 'group_id');
    }

    public function eventTasks()
    {
        return $this->hasMany(EventTask::class, 'group_id');
    }
}
