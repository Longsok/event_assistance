<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventBudgetItem;
use App\Models\BudgetTemplate;

class BudgetEngine
{
    public function generate(Event $event, float $totalBudget): void
    {
        $budget = EventBudget::create([
            'event_id'     => $event->id,
            'total_budget' => $totalBudget,
        ]);

        $templates = BudgetTemplate::where('category_id', $event->category_id)
            ->orderBy('sort_order')
            ->get();

        $included = $templates->filter(
            fn($t) => $this->passesRules($t->scale_trigger, $event)
        );

        foreach ($included as $template) {
            $suggestedAmount = ($template->suggested_percentage / 100) * $totalBudget;

            EventBudgetItem::create([
                'event_budget_id'  => $budget->id,
                'line_item'        => $template->line_item,
                'suggested_amount' => round($suggestedAmount, 2),
                'allocated_amount' => round($suggestedAmount, 2),
                'actual_amount'    => 0,
                'is_custom'        => false,
                'sort_order'       => $template->sort_order,
            ]);
        }
    }

    public function recalculate(Event $event, float $newTotalBudget): void
    {
        $budget = $event->budget;
        if (!$budget) return;

        $budget->update(['total_budget' => $newTotalBudget]);

        $templates = BudgetTemplate::where('category_id', $event->category_id)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('line_item');

        $systemItems = $budget->items()->where('is_custom', false)->get();

        foreach ($systemItems as $item) {
            $template = $templates->get($item->line_item);
            if (!$template) continue;

            $newSuggested = round(
                ($template->suggested_percentage / 100) * $newTotalBudget, 2
            );

            $item->update([
                'suggested_amount' => $newSuggested,
                'allocated_amount' => $newSuggested,
            ]);
        }
    }

    public function getSummary(Event $event): array
    {
        $budget = $event->budget()->with('items')->first();

        if (!$budget) {
            return [
                'has_budget'      => false,
                'total_budget'    => 0,
                'total_allocated' => 0,
                'total_actual'    => 0,
                'unallocated'     => 0,
                'over_budget'     => false,
            ];
        }

        $totalAllocated = $budget->items->sum('allocated_amount');
        $totalActual    = $budget->items->sum('actual_amount');

        return [
            'has_budget'      => true,
            'total_budget'    => $budget->total_budget,
            'total_allocated' => $totalAllocated,
            'total_actual'    => $totalActual,
            'unallocated'     => $budget->total_budget - $totalAllocated,
            'over_budget'     => $totalActual > $budget->total_budget,
            'items'           => $budget->items,
        ];
    }

    private function passesRules(string $trigger, Event $event): bool
    {
        if ($trigger === 'any' || empty($trigger)) return true;
        if (preg_match('/capacity\s*>\s*(\d+)/', $trigger, $m))  return $event->capacity > (int) $m[1];
        if (preg_match('/capacity\s*<=\s*(\d+)/', $trigger, $m)) return $event->capacity <= (int) $m[1];
        if (preg_match('/venue\s*=\s*(\w+)/', $trigger, $m))     return $event->venue_type === $m[1];
        if (preg_match('/meal\s*=\s*(yes|no)/', $trigger, $m))   return $event->meal_provided === ($m[1] === 'yes');
        if (preg_match('/days\s*>\s*(\d+)/', $trigger, $m))      return $event->total_days > (int) $m[1];
        return true;
    }
}
