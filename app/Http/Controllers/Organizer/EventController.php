<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Services\TimelineEngine;
use App\Services\ScheduleEngine;
use App\Services\BudgetEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct(
        private TimelineEngine $timelineEngine,
        private ScheduleEngine $scheduleEngine,
        private BudgetEngine   $budgetEngine,
    ) {}

    public function index(Request $request)
    {
        $events = Event::where('user_id', auth()->id())
            ->with('category')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->withCount('eventGuests')
            ->orderBy('start_date')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $categories = EventCategory::where('is_active', true)->get();
        return view('events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'category_id'   => 'required|exists:event_categories,id',
            'start_date'    => 'required|date|after_or_equal:today',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'start_time'    => 'nullable|string',
            'end_time'      => 'nullable|string',
            'venue'         => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'province' => 'required|string|max:100',
            'capacity'      => 'required|integer|min:1',
            'venue_type'    => 'required|in:indoor,outdoor,hybrid',
            'meal_provided' => 'boolean',
            'total_budget'  => 'nullable|numeric|min:0',
            'style_pref'    => 'nullable|string|max:50',
            'description'   => 'nullable|string',
        ]);

        $event = Event::create([
            ...$request->only([
                'title', 'category_id', 'start_date', 'end_date',
                'start_time', 'end_time', 'venue', 'address',
                'capacity', 'venue_type', 'description', 'province',
            ]),
            'user_id'          => auth()->id(),
            'meal_provided'    => $request->boolean('meal_provided'),
            'is_public'        => $request->boolean('is_public'),
            'status'           => 'draft',
            'slug'             => Str::slug($request->title) . '-' . Str::random(6),
            'invite_token'     => Str::uuid(),
            'attendance_token' => Str::uuid(),
        ]);

        // Generate timeline and schedule
        $this->timelineEngine->generate($event);
        $this->scheduleEngine->generate($event);

        // Always generate budget — uses user input or estimates from capacity if not provided
        $this->budgetEngine->generate($event, (float)($request->total_budget ?? 0));

        // Store preferences in session for AI suggestions page (live only — not in DB)
        $province = $request->province ?? 'Phnom Penh';
        session(['event_prefs_' . $event->id => [
            'budget'     => (float)($request->total_budget ?? 0),
            'style'      => $request->style_pref ?? 'modern',
            'venue_pref' => $request->venue_type ?? 'indoor',
            'meal'       => $request->boolean('meal_provided') ? 'buffet' : 'no meal',
            'province'   => $province,
        ]]);

        return redirect()->route('events.suggestions.show', $event)
            ->with('success', 'Event created! Here are our AI recommendations for ' . $province . '.');
    }

    public function show(Event $event)
    {
        $this->authorizeEvent($event);

        $event->load([
            'category',
            'tasks.group',
            'schedules',
            'budget.items',
            'eventGuests.guest',
            'contributions.guest',
            'completion',
            'inviteCard',
        ]);

        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);
        $categories = EventCategory::where('is_active', true)->get();
        return view('events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'title'         => 'required|string|max:255',
            'category_id'   => 'required|exists:event_categories,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'capacity'      => 'required|integer|min:1',
            'venue_type'    => 'required|in:indoor,outdoor,hybrid',
            'province'      => 'nullable|string|max:100',
            'status'        => 'nullable|in:draft,published,ongoing,completed,cancelled',
            'is_public'     => 'nullable|boolean',
            'meal_provided' => 'nullable|boolean',
        ]);

        $event->update([
            ...$request->only([
                'title', 'category_id', 'start_date', 'end_date',
                'start_time', 'end_time', 'venue', 'address',
                'capacity', 'venue_type', 'description', 'status', 'province',
            ]),
            'is_public'     => $request->boolean('is_public'),
            'meal_provided' => $request->boolean('meal_provided'),
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        $event->delete();
        return redirect()->route('events.index')
            ->with('success', 'Event deleted.');
    }

    public function updateStatus(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $request->validate([
            'status' => 'required|in:draft,published,ongoing,completed,archived',
        ]);

        $event->update(['status' => $request->status]);

        return back()->with('success', 'Event status updated.');
    }

    private function authorizeEvent(Event $event): void
    {
        abort_if($event->user_id !== auth()->id(), 403);
    }

    public function cancel(Event $event)
{
    // Make sure organizer owns this event
    if ($event->user_id !== auth()->id()) {
        abort(403);
    }

    // Only allow cancelling if event is not already completed
    if ($event->status === 'completed') {
        return back()->with('error', 'Cannot cancel a completed event.');
    }

    $event->update(['status' => 'cancelled']);

    return back()->with('success', 'Event has been cancelled.');
}
}
