<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleTemplate;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class ScheduleTemplateController extends Controller
{
    public function index(EventCategory $category)
    {
        $templates  = $category->scheduleTemplates()->orderBy('sort_order')->get();
        $categories = EventCategory::all();

        return view('admin.templates.schedule', compact(
            'category',
            'templates',
            'categories'
        ));
    }

    public function store(Request $request, EventCategory $category)
    {
        $request->validate([
            'session_name'     => 'required|string|max:255',
            'anchor'           => 'required|in:start,end,middle,proportional',
            'offset_minutes'   => 'required|integer',
            'duration_minutes' => 'required|integer|min:1',
            'is_break'         => 'boolean',
            'scale_trigger'    => 'nullable|string|max:100',
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

    public function update(Request $request, EventCategory $category, ScheduleTemplate $template)
    {
        $request->validate([
            'session_name'     => 'required|string|max:255',
            'anchor'           => 'required|in:start,end,middle,proportional',
            'offset_minutes'   => 'required|integer',
            'duration_minutes' => 'required|integer|min:1',
            'is_break'         => 'boolean',
            'scale_trigger'    => 'nullable|string|max:100',
        ]);

        $template->update($request->only([
            'session_name',
            'anchor',
            'offset_minutes',
            'duration_minutes',
            'is_break',
            'scale_trigger',
        ]));

        return back()->with('success', 'Schedule session updated.');
    }

    public function destroy(EventCategory $category, ScheduleTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Schedule session deleted.');
    }

    public function reorder(Request $request, EventCategory $category)
    {
        foreach ($request->order as $position => $id) {
            ScheduleTemplate::where('id', $id)
                ->update(['sort_order' => $position + 1]);
        }
        return response()->json(['success' => true]);
    }
}
