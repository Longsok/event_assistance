<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventBudget;
use Livewire\Component;

class BudgetTracker extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function updateActual(int $itemId, $value): void
    {
        $budget = EventBudget::where('event_id', $this->event->id)->first();
        if (!$budget) return;

        $item = $budget->items()->find($itemId);
        if (!$item) return;

        $item->update(['actual_amount' => max(0, (float)$value)]);
    }

    protected function getBudgetData(): array
    {
        $budget = EventBudget::with('items')
            ->where('event_id', $this->event->id)
            ->first();

        if (!$budget) {
            return [
                'budget'  => null,
                'summary' => [
                    'has_budget'      => false,
                    'total_budget'    => 0,
                    'total_allocated' => 0,
                    'total_actual'    => 0,
                    'unallocated'     => 0,
                    'over_budget'     => false,
                ],
            ];
        }

        $totalBudget    = (float)$budget->total_budget;
        $totalAllocated = (float)$budget->items->sum('allocated_amount');
        $totalActual    = (float)$budget->items->sum('actual_amount');
        $unallocated    = $totalBudget - $totalActual;

        return [
            'budget'  => $budget,
            'summary' => [
                'has_budget'      => true,
                'total_budget'    => $totalBudget,
                'total_allocated' => $totalAllocated,
                'total_actual'    => $totalActual,
                'unallocated'     => $unallocated,
                'over_budget'     => $totalActual > $totalBudget,
            ],
        ];
    }

    public function render()
    {
        $data = $this->getBudgetData();

        return view('livewire.budget-tracker', [
            'budget'  => $data['budget'],
            'summary' => $data['summary'],
        ]);
    }
}
