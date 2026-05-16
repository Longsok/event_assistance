<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventBudgetItem;
use App\Services\BudgetEngine;
use Livewire\Component;

class BudgetTracker extends Component
{
    public Event  $event;
    public array  $summary = [];

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->loadSummary();
    }

    public function loadSummary(): void
    {
        $this->summary = app(BudgetEngine::class)->getSummary($this->event);
    }

    /**
     * Update actual amount spent on a budget item inline.
     */
    public function updateActual(int $itemId, float $amount): void
    {
        $item = EventBudgetItem::findOrFail($itemId);
        $item->update(['actual_amount' => max(0, $amount)]);
        $this->loadSummary();
    }

    public function render()
    {
        return view('livewire.budget-tracker', [
            'budget'  => $this->event->budget()->with('items')->first(),
            'summary' => $this->summary,
        ]);
    }
}
