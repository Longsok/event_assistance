<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Guest;
use App\Models\EventCategory;
use Illuminate\Http\Request;

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

        $recentEvents    = Event::with(['user', 'category'])->latest()->take(10)->get();
        $recentUsers     = User::latest()->take(7)->get();
        $eventByCategory = EventCategory::withCount('events')->having('events_count', '>', 0)->get();
        $recentActivity  = $this->getRecentActivity();

        $tamplateSummary = EventCategory::with([
            'categoryTemplates',
            'scheduleTemplates',
            'budgetTemplates',
        ])->withCount([
            'categoryTemplates',
            'scheduleTemplates',
            'budgetTemplates',
        ])->get();

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
        $event = Event::with('user')->latest()->take(5)->get()->map(fn($e) => [
    'type'    => 'Event Created',
    'message' => ($e->user?->name ?? 'Unknown') . " created {$e->title}",
    'time'    => $e->created_at,
]);

        $user = User::latest()->take(5)->get()->map(fn($u) => [
            'type'    => 'User Registered',
            'message' => "new user {$u->name} registered",
            'time'    => $u->created_at,
        ]);

        return collect($event)
            ->merge($user)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
    }
}
