<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTask;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth() ->user();

        $stats = [
            'total_events' => Event::where('user_id', $user ->id) ->count(),
            'total_guests' => $user ->guests() ->count(),
            'overdue_tasks' => EventTask::whereHas('event', fn($q) =>[
                                $q ->where('user_id', $user ->id) ->where('status', 'overdue')
                            ]) ->count(),
            'total_contributions' => $user ->events() ->with('contributions')
                                            ->get() ->flatMap ->contributions ->where('status', 'received')
                                            ->sum('amount'),
        ];

        $upcomingEvents = Event::where('user_id', $user->id)
            ->whereIn('status', ['draft', 'published', 'ongoing'])
            ->with('category')
            ->withCount('eventGuests')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $overdueTasks = EventTask::whereHas('event', fn($q) =>
                            $q->where('user_id', $user->id)
                        )
                        ->with(['event', 'group'])
                        ->where('status', 'overdue')
                        ->orderBy('due_date')
                        ->take(5)
                        ->get();

        $recentCheckIns = \App\Models\AttendanceLog::whereHas('eventGuest.event', fn($q) =>
                              $q->where('user_id', $user->id)
                          )
                          ->with('eventGuest.guest')
                          ->latest('checked_in_at')
                          ->take(5)
                          ->get();

        return view('dashboard.organizer', compact(
            'stats',
            'upcomingEvents',
            'overdueTasks',
            'recentCheckIns'
        ));
    }

}
