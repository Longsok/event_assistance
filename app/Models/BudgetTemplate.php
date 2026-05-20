<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTemplate extends Model
{
    protected $table = 'budget_tamplates';
    
    protected $fillable = [
        'category_id',
        'line_item',
        'suggested_percentage',
        'scale_trigger',
        'sort_order',
    ];

    protected $casts = [
        'suggested_percentage' => 'decimal:2',
    ];

    // ─── Relationships ───────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }
}
