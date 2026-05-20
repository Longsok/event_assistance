<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryTemplate;
use App\Models\EventCategory;
use App\Models\TaskGroup;
use Illuminate\Http\Request;

class CategoryTemplateController extends Controller
{
    public function index(EventCategory $category)
    {
        $templates  = $category->categoryTemplates()->with('group')->get();
        $groups     = TaskGroup::orderBy('sort_order')->get();
        $categories = EventCategory::all();

        return view('admin.templates.timeline', compact(
            'category',
            'templates',
            'groups',
            'categories'
        ));
    }

    public function store(Request $request, EventCategory $category)
    {
        $request->validate([
            'task_name'     => 'required|string|max:255',
            'group_id'      => 'required|exists:task_groups,id',
            'days_before'   => 'required|integer',
            'anchor'        => 'required|in:before_event,first_day,last_day,after_event,proportional',
            'priority'      => 'required|in:high,medium,low',
            'scale_trigger' => 'nullable|string|max:100',
        ]);

        $category->categoryTemplates()->create([
            'group_id'      => $request->group_id,
            'task_name'     => $request->task_name,
            'days_before'   => $request->days_before,
            'anchor'        => $request->anchor,
            'priority'      => $request->priority,
            'scale_trigger' => $request->scale_trigger ?? 'any',
            'sort_order'    => $category->categoryTemplates()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Task template added.');
    }

    public function update(Request $request, EventCategory $category, CategoryTemplate $template)
    {
        $request->validate([
            'task_name'     => 'required|string|max:255',
            'group_id'      => 'required|exists:task_groups,id',
            'days_before'   => 'required|integer',
            'anchor'        => 'required|in:before_event,first_day,last_day,after_event,proportional',
            'priority'      => 'required|in:high,medium,low',
            'scale_trigger' => 'nullable|string|max:100',
        ]);

        $template->update($request->only([
            'task_name',
            'group_id',
            'days_before',
            'anchor',
            'priority',
            'scale_trigger',
            'notes',
        ]));

        return back()->with('success', 'Task template updated.');
    }

    public function destroy(EventCategory $category, CategoryTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Task template deleted.');
    }

    // Drag to reorder
    public function reorder(Request $request, EventCategory $category)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'exists:category_templates,id',
        ]);

        foreach ($request->order as $position => $id) {
            CategoryTemplate::where('id', $id)
                ->update(['sort_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }
}
