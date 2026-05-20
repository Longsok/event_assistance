<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetTemplate;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class BudgetTemplateController extends Controller
{
    // Show category picker
    public function index()
    {
        $categories = EventCategory::withCount('budgetTemplates')->get();
        return view('admin.templates.budget-index', compact('categories'));
    }

    // Show budget templates for a specific category
    public function show(EventCategory $category)
    {
        $templates = $category->budgetTemplates()->orderBy('sort_order')->get();
        return view('admin.templates.budget', compact('category', 'templates'));
    }

    public function store(Request $request, EventCategory $category)
    {
        $request->validate([
            'line_item'            => 'required|string|max:255',
            'suggested_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $category->budgetTemplates()->create([
            'line_item'            => $request->line_item,
            'suggested_percentage' => $request->suggested_percentage,
            'scale_trigger'        => $request->scale_trigger ?? 'any',
            'sort_order'           => $category->budgetTemplates()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Budget item added.');
    }

    public function destroy(EventCategory $category, BudgetTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Budget item deleted.');
    }
}
