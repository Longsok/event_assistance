<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Guest;
use App\Models\EventCategory;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stat = [
            'total_users'    => User::count(),
            'total_events'   => Event::count(),
            'total_guests'   => Guest::count(),
            'ongoing_events' => Event::where('status', 'ongoing')->count(),
        ];

        $recentEvents = Event::with(['user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        $recentUsers = User::latest()->take(7)->get();

        $eventByCategory = EventCategory::withCount('events')
            ->get()
            ->filter(fn($c) => $c->events_count > 0);

        $tamplateSummary = EventCategory::withCount([
            'categoryTemplates',
            'scheduleTemplates',
            'budgetTemplates',
        ])->get();

        $recentActivity = $this->getRecentActivity();

        return view('admin.dashboard', compact(
            'stat',
            'recentEvents',
            'recentUsers',
            'eventByCategory',
            'recentActivity',
            'tamplateSummary',
        ));
    }

    private function getRecentActivity(): array
    {
        $events = Event::with('user')->latest()->take(5)->get()->map(fn($e) => [
            'type'    => 'Event Created',
            'message' => ($e->user?->name ?? 'Unknown') . " created {$e->title}",
            'time'    => $e->created_at,
        ]);

        $users = User::latest()->take(5)->get()->map(fn($u) => [
            'type'    => 'User Registered',
            'message' => "New user {$u->name} registered",
            'time'    => $u->created_at,
        ]);

        return collect($events)
            ->merge($users)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
    }
}
