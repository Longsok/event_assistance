<?php

namespace App\Models;

<<<<<<< HEAD
use App\Models\CategoryTemplate;
=======
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
use Illuminate\Database\Eloquent\Model;

class TaskGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
    ];
<<<<<<< HEAD

    // ─── Relationships ───────────────────────────────────────

=======
 
    // ─── Relationships ───────────────────────────────────────
 
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    // Category templates belonging to this group
    public function categoryTemplates()
    {
        return $this->hasMany(CategoryTemplate::class, 'group_id');
    }
<<<<<<< HEAD

=======
 
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    // Event tasks belonging to this group
    public function eventTasks()
    {
        return $this->hasMany(EventTask::class, 'group_id');
    }
}
