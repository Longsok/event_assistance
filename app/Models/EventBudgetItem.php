<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBudgetItem extends Model
{
     protected $fillable = [
        'event_budget_id',
        'line_item',
        'suggested_amount',
        'allocated_amount',
        'actual_amount',
        'is_custom',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'suggested_amount' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'actual_amount'    => 'decimal:2',
        'is_custom'        => 'boolean',
    ];

    // ─── Helpers ─────────────────────────────────────────────

    public function isOverBudget(): bool
    {
        return $this->actual_amount > $this->allocated_amount;
    }

    public function getRemainingAttribute(): float
    {
        return $this->allocated_amount - $this->actual_amount;
    }

    // ─── Relationships ───────────────────────────────────────

    public function budget()
    {
        return $this->belongsTo(EventBudget::class, 'event_budget_id');
    }
}
