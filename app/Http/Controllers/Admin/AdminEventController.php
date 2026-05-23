<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    public function index()
    {
        $events = Event::with(['user', 'category'])
            ->withCount('eventGuests')
            ->latest()
            ->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load([
            'user',
            'category',
            'tasks',
            'eventGuests',
            'budget',
            'schedules',
        ]);

        return view('admin.events.show', compact('event'));
    }

    public function create()
    {
        $organizers = User::role('organizer')->orderBy('name')->get();
        $categories = EventCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.events.create', compact('organizers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'capacity'    => 'required|integer|min:1',
            'venue_type'  => 'required|in:indoor,outdoor,hybrid',
        ]);

        $event = Event::create([
            'user_id'       => $request->user_id,
            'title'         => $request->title,
            'category_id'   => $request->category_id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'venue'         => $request->venue,
            'capacity'      => $request->capacity,
            'venue_type'    => $request->venue_type,
            'status'        => $request->status ?? 'draft',
            'description'   => $request->description,
            'meal_provided' => $request->boolean('meal_provided'),
            'slug'          => Str::slug($request->title) . '-' . Str::random(6),
            'invite_token'  => Str::uuid(),
        ]);

        // Auto-generate timeline, budget and schedule
        app(\App\Services\TimelineEngine::class)->generate($event);
        app(\App\Services\BudgetEngine::class)->generate($event);
        app(\App\Services\ScheduleEngine::class)->generate($event);

        return redirect()->route('admin.events.show', $event)
            ->with('success', "Event '{$event->title}' created successfully.");
    }

    public function destroy(Event $event)
    {
        $title = $event->title;
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$title}' deleted successfully.");
    }
}
