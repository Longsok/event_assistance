<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\Event;
=======
use App\model\Event;
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
<<<<<<< HEAD
    public function index(Request $request)
    {
        $events = Event::with(['user', 'category'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->latest()
            ->get();

        return view('admin.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load(['user', 'category']);
        return view('admin.events.show', compact('event'));
    }
=======
    public function index(Request $request){
        $event = Event::with([user], [category])
                ->when($request ->status, fn($q) =>$q ->where('status', $request ->status))
                ->when($request ->search, fn($q) =>$q ->where('title', 'like', '%' .$request ->search . '%'))
                ->latest()
                ->get();

        return view('admin.events.index', compact('events'));
    }
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
}
