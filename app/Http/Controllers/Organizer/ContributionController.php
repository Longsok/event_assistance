<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventContribution;
use App\Models\Guest;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizeEvent($event);

        $contributions = $event->contributions() ->with('guest') ->latest() ->get();

        $stats = [
            'total_expected' => $contributions->sum('amount'),
            'total_received' => $contributions->where('status', 'received')->sum('amount'),
            'total_pending'  => $contributions->where('status', 'pending')->sum('amount'),
            'count'          => $contributions->count(),
        ];

        // Guests registered to this event for dropdown
        $eventGuests = $event->eventGuests()->with('guest')->get();

        return view('contributions.index', compact(
            'event',
            'contributions',
            'stats',
            'eventGuests'
        ));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'guest_id'         => 'required|exists:guests,id',
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,bank_transfer,other',
            'reference_number' => 'nullable|string|max:100',
            'status'           => 'required|in:pending,received',
            'notes'            => 'nullable|string',
        ]);

        EventContribution::create([
            'event_id'         => $event->id,
            'guest_id'         => $request->guest_id,
            'amount'           => $request->amount,
            'payment_method'   => $request->payment_method,
            'reference_number' => $request->reference_number,
            'status'           => $request->status,
            'notes'            => $request->notes,
            'recorded_by'      => auth()->id(),
            'paid_at'          => $request->status === 'received' ? now() : null,
        ]);

        return back()->with('success', 'Contribution recorded.');
    }

    public function update(Request $request, Event $event, EventContribution $contribution)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,bank_transfer,other',
            'status'           => 'required|in:pending,received,cancelled',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);

        $contribution->update([
            ...$request->only([
                'amount',
                'payment_method',
                'reference_number',
                'status',
                'notes',
            ]),
            'paid_at' => $request->status === 'received' ? now() : null,
        ]);

        return back()->with('success', 'Contribution updated.');
    }

    public function destroy(Event $event, EventContribution $contribution)
    {
        $this->authorizeEvent($event);
        $contribution->delete();
        return back()->with('success', 'Contribution deleted.');
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }
}
