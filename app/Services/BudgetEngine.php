<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventBudgetItem;
use Carbon\Carbon;

class BudgetEngine
{
    /**
     * Meal cost estimates per guest (in local currency).
     * Adjustable here or in config.
     */
    protected array $mealCosts = [
        'breakfast' => 5,
        'lunch'     => 10,
        'dinner'    => 15,
    ];

    /**
     * Per-day staff/operational cost estimate.
     */
    protected int $staffCostPerDay = 200;

    /**
     * Generate a budget for the event based on:
     * - Category templates (percentage-based line items)
     * - Number of days × guests × meal costs
     * - Venue type adjustments
     */
    public function generate(Event $event): void
    {
        $templates = $event->category?->budgetTemplates()->get();

        if (!$templates || $templates->isEmpty()) {
            return;
        }

        // Get or create the event's budget record
        $budget = EventBudget::firstOrCreate(
            ['event_id' => $event->id],
            ['total_budget' => $event->total_budget ?? 0]
        );

        // If a total budget was already set by the organizer, use it.
        // Otherwise estimate it from guests + days.
        $totalBudget = (float)($budget->total_budget);
        if ($totalBudget <= 0) {
            $totalBudget = $this->estimateTotalBudget($event);
            $budget->update(['total_budget' => $totalBudget]);
        }

        // Delete existing auto-generated items before regenerating
        $budget->items()->where('is_custom', false)->delete();

        foreach ($templates as $template) {
            if (!$this->passesTrigger($template, $event)) {
                continue;
            }

            $percentage    = (float)($template->suggested_percentage ?? 0);
            $baseSuggested = round($totalBudget * $percentage / 100, 2);

            // Scale some line items by days or guests
            $scaledAmount = $this->scaleAmount(
                $template->line_item,
                $baseSuggested,
                $event
            );

            EventBudgetItem::create([
                'event_budget_id'  => $budget->id,
                'line_item'        => $template->line_item,
                'suggested_amount' => $scaledAmount,
                'allocated_amount' => $scaledAmount,
                'actual_amount'    => 0,
                'is_custom'        => false,
                'sort_order'       => $template->sort_order ?? 0,
            ]);
        }

        // Add auto-generated meal budget items for multi-day events
        $durationDays = $this->getEventDuration($event);
        $capacity     = max(1, (int)($event->capacity ?? 1));

        if ($durationDays > 1) {
            $this->addMealItems($budget, $event, $durationDays, $capacity);
        }

        // Add per-day operational cost item if multi-day
        if ($durationDays > 1) {
            $staffTotal = $this->staffCostPerDay * $durationDays;
            EventBudgetItem::create([
                'event_budget_id'  => $budget->id,
                'line_item'        => "Staff & Operations ({$durationDays} days)",
                'suggested_amount' => $staffTotal,
                'allocated_amount' => $staffTotal,
                'actual_amount'    => 0,
                'is_custom'        => false,
                'sort_order'       => 90,
            ]);
        }
    }

    /**
     * Estimate a reasonable total budget when the organizer hasn't set one.
     *
     * Formula:
     *   base = capacity × perGuestBase × durationDays × venueMultiplier
     */
    protected function estimateTotalBudget(Event $event): float
    {
        $capacity    = max(1, (int)($event->capacity ?? 50));
        $duration    = max(1, $this->getEventDuration($event));
        $perGuest    = 50; // base $50 per guest per day

        // Venue type multiplier
        $venueMultiplier = match (strtolower($event->venue_type ?? 'indoor')) {
            'outdoor' => 1.3,
            'hybrid'  => 1.5,
            default   => 1.0, // indoor
        };

        // Event duration multiplier (discount for very long events)
        $durationMultiplier = match (true) {
            $duration <= 1  => 1.0,
            $duration <= 3  => 0.95,
            $duration <= 7  => 0.85,
            default         => 0.75,
        };

        return round(
            $capacity * $perGuest * $duration * $venueMultiplier * $durationMultiplier,
            -2  // round to nearest 100
        );
    }

    /**
     * Scale a budget line item by days or guests where appropriate.
     * Catering, Meals, Accommodation scale; fixed costs like Venue don't.
     */
    protected function scaleAmount(string $lineItem, float $baseAmount, Event $event): float
    {
        $duration = $this->getEventDuration($event);
        $capacity = max(1, (int)($event->capacity ?? 1));
        $lower    = strtolower($lineItem);

        $scalesWithDays = str_contains($lower, 'catering')
            || str_contains($lower, 'meal')
            || str_contains($lower, 'food')
            || str_contains($lower, 'accommodation')
            || str_contains($lower, 'hotel')
            || str_contains($lower, 'staff')
            || str_contains($lower, 'security');

        if ($scalesWithDays && $duration > 1) {
            // Scale proportionally (but don't just multiply — use sqrt to dampen)
            return round($baseAmount * sqrt($duration), 2);
        }

        return round($baseAmount, 2);
    }

    /**
     * Add individual meal line items for each meal type per day.
     * Breakfast: $5/guest, Lunch: $10/guest, Dinner: $15/guest
     */
    protected function addMealItems(
        EventBudget $budget,
        Event $event,
        int $durationDays,
        int $capacity
    ): void {
        $mealProvided = (bool)($event->meal_provided ?? true);
        if (!$mealProvided) return;

        $sort = 50;
        foreach ($this->mealCosts as $meal => $costPerGuest) {
            $total = $costPerGuest * $capacity * $durationDays;
            EventBudgetItem::create([
                'event_budget_id'  => $budget->id,
                'line_item'        => ucfirst($meal) . " ({$capacity} guests × {$durationDays} days × \${$costPerGuest})",
                'suggested_amount' => $total,
                'allocated_amount' => $total,
                'actual_amount'    => 0,
                'is_custom'        => false,
                'sort_order'       => $sort++,
            ]);
        }
    }

    /**
     * Get event duration in days (inclusive).
     */
    protected function getEventDuration(Event $event): int
    {
        $start = Carbon::parse($event->start_date);
        $end   = Carbon::parse($event->end_date);
        return max(1, $start->diffInDays($end) + 1);
    }

    /**
     * Check scale_trigger conditions (same logic as TimelineEngine).
     */
    protected function passesTrigger($template, Event $event): bool
    {
        $trigger = trim($template->scale_trigger ?? 'any');
        if ($trigger === '' || $trigger === 'any') return true;

        $duration = $this->getEventDuration($event);

        if (preg_match('/^(\w+)\s*(>|<|>=|<=|=|!=)\s*(.+)$/', $trigger, $m)) {
            [, $field, $op, $value] = $m;
            $actual = match ($field) {
                'capacity' => (int)($event->capacity ?? 0),
                'duration' => $duration,
                'venue'    => strtolower($event->venue_type ?? ''),
                default    => null,
            };
            if ($actual === null) return true;
            return match ($op) {
                '>'  => $actual > (int)$value,
                '<'  => $actual < (int)$value,
                '>=' => $actual >= (int)$value,
                '<=' => $actual <= (int)$value,
                '='  => (string)$actual === strtolower($value),
                '!=' => (string)$actual !== strtolower($value),
                default => true,
            };
        }
        return true;
    }
}
