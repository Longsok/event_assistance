<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
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
}
