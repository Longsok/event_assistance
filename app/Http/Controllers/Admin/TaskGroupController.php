<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskGroupController extends Controller
{
    public function index()
    {
        $groups = TaskGroup::withCount('eventTasks')
            ->orderBy('sort_order')
            ->get();

        return view('admin.task-groups.index', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:task_groups',
            'color' => 'nullable|string|max:7',
        ]);

        TaskGroup::create([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'color'      => $request->color ?? '#534AB7',
            'sort_order' => TaskGroup::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Task group added.');
    }

    public function update(Request $request, TaskGroup $taskGroup)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:task_groups,name,' . $taskGroup->id,
            'color' => 'nullable|string|max:7',
        ]);

        $taskGroup->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'color' => $request->color,
        ]);

        return back()->with('success', 'Task group updated.');
    }

    public function destroy(TaskGroup $taskGroup)
    {
        if ($taskGroup->eventTasks()->count() > 0) {
            return back()->with('error', 'Cannot delete group with existing tasks.');
        }

        $taskGroup->delete();
        return back()->with('success', 'Task group deleted.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $position => $id) {
            TaskGroup::where('id', $id)
                ->update(['sort_order' => $position + 1]);
        }
        return response()->json(['success' => true]);
    }
}
