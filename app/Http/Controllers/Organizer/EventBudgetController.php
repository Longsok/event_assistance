<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventBudget;
use App\Models\EventBudgetItem;
use Illuminate\Http\Request;

class EventBudgetController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizeEvent($event);

        $budget = $event->budget()->with('items')->first();

        return view('budget.index', compact('event', 'budget'));
    }

    // Update total budget
    public function updateTotal(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'total_budget' => 'required|numeric|min:0',
        ]);

        $event->budget()->updateOrCreate(
            ['event_id' => $event->id],
            ['total_budget' => $request->total_budget]
        );

        return back()->with('success', 'Budget updated.');
    }

    // Add custom budget item
    public function storeItem(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'line_item'        => 'required|string|max:255',
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        $budget = $event->budget;

        EventBudgetItem::create([
            'event_budget_id'  => $budget->id,
            'line_item'        => $request->line_item,
            'suggested_amount' => $request->allocated_amount,
            'allocated_amount' => $request->allocated_amount,
            'actual_amount'    => 0,
            'is_custom'        => true,
            'sort_order'       => $budget->items()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Budget item added.');
    }

    // Update a budget item (allocated or actual amount)
    public function updateItem(Request $request, Event $event, EventBudgetItem $item)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'line_item'        => 'required|string|max:255',
            'allocated_amount' => 'required|numeric|min:0',
            'actual_amount'    => 'required|numeric|min:0',
        ]);

        $item->update($request->only([
            'line_item',
            'allocated_amount',
            'actual_amount',
            'notes',
        ]));

        return back()->with('success', 'Budget item updated.');
    }

    public function destroyItem(Event $event, EventBudgetItem $item)
    {
        $this->authorizeEvent($event);
        $item->delete();
        return back()->with('success', 'Budget item removed.');
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
