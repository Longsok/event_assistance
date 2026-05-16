<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\model\Event;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    public function index(Request $request){
        $event = Event::with([user], [category])
                ->when($request ->status, fn($q) =>$q ->where('status', $request ->status))
                ->when($request ->search, fn($q) =>$q ->where('title', 'like', '%' .$request ->search . '%'))
                ->latest()
                ->get();

        return view('admin.events.index', compact('events'));
    }
}
