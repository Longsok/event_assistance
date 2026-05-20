<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::withCount([
            'categoryTemplates',
            'scheduleTemplates',
            'budgetTemplates',
            'events'
        ])->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:event_categories',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        EventCategory::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'icon'        => $request->icon,
            'color'       => $request->color ?? '#534AB7',
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category created successfully.');
    }

    public function edit(EventCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, EventCategory $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:event_categories,name,' . $category->id,
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $category->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'icon'        => $request->icon,
            'color'       => $request->color,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    public function destroy(EventCategory $category)
    {
        // Prevent delete if events exist under this category
        if ($category->events()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing events.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}
