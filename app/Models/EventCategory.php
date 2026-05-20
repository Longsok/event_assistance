<?php

namespace App\Models;

use App\Models\BudgetTemplate;
use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'color', 'description', 'is_active'
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    public function categoryTemplates()
    {
        return $this->hasMany(CategoryTemplate::class, 'category_id');
    }

    public function scheduleTemplates()
    {
        return $this->hasMany(ScheduleTemplate::class, 'category_id');
    }

    public function budgetTemplates()
    {
        return $this->hasMany(BudgetTemplate::class, 'category_id');
    }
}
