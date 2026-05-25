<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\AiSuggestionService;
use Illuminate\Http\Request;

class EventSuggestionController extends Controller
{
    public function __construct(private AiSuggestionService $aiService) {}

    public function show(Event $event, Request $request)
    {
        abort_if($event->user_id !== auth()->id(), 403);

        // Get preferences from session
        $prefs = session('event_prefs_' . $event->id, []);

        // Load budget — session first, then DB estimate, then capacity-based estimate
        $budget = 0;
        if (!empty($prefs['budget']) && $prefs['budget'] > 0) {
            $budget = $prefs['budget'];
        } elseif ($event->budget && $event->budget->total_budget > 0) {
            $budget = (float) $event->budget->total_budget;
        } else {
            // Estimate from capacity if nothing set
            $days   = max(1, \Carbon\Carbon::parse($event->start_date)
                ->diffInDays(\Carbon\Carbon::parse($event->end_date)) + 1);
            $budget = $event->capacity * 50 * $days;
        }

        $eventData = [
            'event_type'  => $event->category?->name ?? 'event',
            'guest_count' => $event->capacity,
            'budget'      => $budget,
            'style'       => $prefs['style'] ?? 'modern',
            'venue_pref'  => $event->venue_type ?? 'indoor',
            'meal'        => $event->meal_provided ? 'buffet' : 'no meal',
        ];

        $suggestions = $this->aiService->generateSuggestions($eventData);

        // Always inject real budget into estimated_total
        if (isset($suggestions['estimated_total'])) {
            $suggestions['estimated_total']['budget']        = $budget;
            $suggestions['estimated_total']['venue_cost']    = (int)($budget * 0.40);
            $suggestions['estimated_total']['catering_cost'] = (int)($budget * 0.35);
            $suggestions['estimated_total']['decor_cost']    = (int)($budget * 0.15);
            $suggestions['estimated_total']['note']          = "Based on {$event->capacity} guests with \$" . number_format($budget) . " budget";
        }

        return view('events.suggestions', compact('event', 'suggestions', 'eventData'));
    }

    public function selectVenue(Event $event, Request $request)
    {
        abort_if($event->user_id !== auth()->id(), 403);

        $request->validate([
            'venue'   => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $event->update([
            'venue'   => $request->venue,
            'address' => $request->address ?? $event->address,
        ]);

        return response()->json(['success' => true, 'message' => 'Venue selected!']);
    }

    public function skip(Event $event)
    {
        abort_if($event->user_id !== auth()->id(), 403);
        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }
}
