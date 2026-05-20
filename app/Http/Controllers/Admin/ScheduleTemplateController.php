<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleTemplate;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class ScheduleTemplateController extends Controller
{
    // Show category picker
    public function index()
    {
        $categories = EventCategory::withCount('scheduleTemplates')->get();
        return view('admin.templates.schedule-index', compact('categories'));
    }

    // Show schedule templates for a specific category
    public function show(EventCategory $category)
    {
        $templates = $category->scheduleTemplates()->orderBy('sort_order')->get();
        return view('admin.templates.schedule', compact('category', 'templates'));
    }

    public function store(Request $request, EventCategory $category)
    {
        $request->validate([
            'session_name'     => 'required|string|max:255',
            'anchor'           => 'required|in:start,end,middle,proportional',
            'offset_minutes'   => 'required|integer',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $category->scheduleTemplates()->create([
            'session_name'     => $request->session_name,
            'anchor'           => $request->anchor,
            'offset_minutes'   => $request->offset_minutes,
            'duration_minutes' => $request->duration_minutes,
            'is_break'         => $request->boolean('is_break'),
            'scale_trigger'    => $request->scale_trigger ?? 'any',
            'sort_order'       => $category->scheduleTemplates()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Schedule session added.');
    }

    public function destroy(EventCategory $category, ScheduleTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Schedule session deleted.');
    }
}
