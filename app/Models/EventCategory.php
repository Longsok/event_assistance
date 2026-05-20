<?php

namespace App\Models;

<<<<<<< HEAD
use App\Models\BudgetTemplate;
=======
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = [
<<<<<<< HEAD
        'name', 'slug', 'icon', 'color', 'description', 'is_active'
    ];

=======
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────

>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    public function categoryTemplates()
    {
<<<<<<< HEAD
        return $this->hasMany(CategoryTemplate::class, 'category_id');
=======
        return $this->hasMany(CategoryTemplate::class, 'category_id')
                    ->orderBy('sort_order');
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    }

    public function scheduleTemplates()
    {
<<<<<<< HEAD
        return $this->hasMany(ScheduleTemplate::class, 'category_id');
=======
        return $this->hasMany(ScheduleTemplate::class, 'category_id')
                    ->orderBy('sort_order');
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    }

    public function budgetTemplates()
    {
<<<<<<< HEAD
        return $this->hasMany(BudgetTemplate::class, 'category_id');
=======
        return $this->hasMany(BudgetTemplate::class, 'category_id')
                    ->orderBy('sort_order');
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    }
}
