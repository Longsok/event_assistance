<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventBudgetItem;

class BudgetEngine
{
    public function generate(Event $event, float $totalBudget = 0): void
    {
        // Load category templates
        $templates = $event->category?->budgetTemplates()
            ->orderBy('sort_order')
            ->get();

        // Estimate budget from capacity if not provided
        if ($totalBudget <= 0) {
            $capacity     = max(1, (int)($event->capacity ?? 50));
            $days         = max(1, \Carbon\Carbon::parse($event->start_date)
                ->diffInDays(\Carbon\Carbon::parse($event->end_date)) + 1);
            $venueMulti   = match($event->venue_type ?? 'indoor') {
                'outdoor' => 0.8,
                'hybrid'  => 1.1,
                default   => 1.0,
            };
            $totalBudget = $capacity * 50 * $days * $venueMulti;
        }

        // Create or update the EventBudget record
        $budget = EventBudget::updateOrCreate(
            ['event_id' => $event->id],
            ['total_budget' => $totalBudget]
        );

        // Delete existing auto-generated items
        $budget->items()->where('is_custom', false)->delete();

        // No templates — generate default items
        if (!$templates || $templates->isEmpty()) {
            $this->generateDefaultItems($budget, $totalBudget, $event);
            return;
        }

        // Generate items from templates
        foreach ($templates as $i => $template) {
            $pct       = (float)($template->suggested_percentage ?? 0);
            $allocated = round($totalBudget * ($pct / 100), 2);

            EventBudgetItem::create([
                'event_budget_id'  => $budget->id,
                'line_item'        => $template->line_item,
                'suggested_amount' => $allocated,
                'allocated_amount' => $allocated,
                'actual_amount'    => 0,
                'is_custom'        => false,
                'sort_order'       => $i + 1,
            ]);
        }

        // Add multi-day meal items
        $days = max(1, \Carbon\Carbon::parse($event->start_date)
            ->diffInDays(\Carbon\Carbon::parse($event->end_date)) + 1);

        if ($days > 1 && $event->meal_provided) {
            $capacity = max(1, (int)($event->capacity ?? 50));
            $meals    = [
                ['name' => 'Breakfast', 'cost_per_person' => 5],
                ['name' => 'Lunch',     'cost_per_person' => 10],
                ['name' => 'Dinner',    'cost_per_person' => 15],
            ];
            $maxSort = $templates->count() + 1;
            foreach ($meals as $meal) {
                $amount = $meal['cost_per_person'] * $capacity * $days;
                EventBudgetItem::create([
                    'event_budget_id'  => $budget->id,
                    'line_item'        => $meal['name'] . " ({$days} days)",
                    'suggested_amount' => $amount,
                    'allocated_amount' => $amount,
                    'actual_amount'    => 0,
                    'is_custom'        => false,
                    'sort_order'       => $maxSort++,
                ]);
            }
        }
    }

    private function generateDefaultItems(EventBudget $budget, float $total, Event $event): void
    {
        $defaults = [
            ['line_item' => 'Venue Rental',     'pct' => 40],
            ['line_item' => 'Catering & Food',  'pct' => 30],
            ['line_item' => 'Decoration',       'pct' => 15],
            ['line_item' => 'Photography',      'pct' => 8],
            ['line_item' => 'Miscellaneous',    'pct' => 7],
        ];

        foreach ($defaults as $i => $item) {
            $allocated = round($total * ($item['pct'] / 100), 2);
            EventBudgetItem::create([
                'event_budget_id'  => $budget->id,
                'line_item'        => $item['line_item'],
                'suggested_amount' => $allocated,
                'allocated_amount' => $allocated,
                'actual_amount'    => 0,
                'is_custom'        => false,
                'sort_order'       => $i + 1,
            ]);
        }
    }
}
