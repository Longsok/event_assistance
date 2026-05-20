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
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'total_guests' => Guest::count(),
<<<<<<< HEAD
            'ongoing_events' => Event::where('status', 'ongoing')->count(),
=======
            'ongoing_events' => Event::where('status', 'ongoing')->count(),       
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
        ];

        $recentEvents = Event::with(['user', 'category']) ->latest() ->take(10) ->get();
        $recentUsers = User::latest() ->take(7) ->get();
        $eventByCategory = EventCategory::withCount('events') ->having('events_count', '>', 0) ->get();
        $recentActivity = $this->getRecentActivity();
        $tamplateSummary = EventCategory::with([
<<<<<<< HEAD
            'categoryTemplates',
            'scheduleTemplates',
            'budgetTemplates'
        ])->withCount([
            'categoryTemplates',
            'scheduleTemplates',
            'budgetTemplates'
        ])->get();
=======
            'CategoryTamplates', 
            'ScheduleTampletes', 
            'BudgetTamplates'
        ]) ->get();
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882

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
        $event = Event::with('user') ->latest() ->take(5) ->get() ->map(fn($e)=> [
            'type' => 'Event Created',
            'message' => "{$e ->user ->name} created {$e ->title}",
            'time' => $e ->created_at,
        ]);

        $user = User::latest() ->take(5) ->get() ->map(fn($u)=> [
            'type' => 'User Registered',
            'message' => "new user {$u ->name} registered",
            'time' => $u ->created_at,
        ]);

<<<<<<< HEAD
        return collect($event)
=======
        return collect($event) 
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
            ->merge($user)
            ->sortByDesc('time')
            ->take(10)
            ->values()
            ->toArray();
   }
<<<<<<< HEAD
}
=======
}
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
