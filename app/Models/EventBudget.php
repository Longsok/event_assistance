<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBudget extends Model
{
    protected $fillable = [
        'event_id',
        'total_budget',
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
    ];

    // ─── Helpers ─────────────────────────────────────────────

    public function getTotalAllocatedAttribute(): float
    {
        return $this->items->sum('allocated_amount');
    }

    public function getTotalActualAttribute(): float
    {
        return $this->items->sum('actual_amount');
    }

    public function getUnallocatedAttribute(): float
    {
        return $this->total_budget - $this->getTotalAllocatedAttribute();
    }

    // ─── Relationships ───────────────────────────────────────

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function items()
    {
        return $this->hasMany(EventBudgetItem::class)->orderBy('sort_order');
    }
}
