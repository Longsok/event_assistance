<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetTemplate;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class BudgetTemplateController extends Controller
{
    public function index(EventCategory $category)
    {
        $templates  = $category->budgetTemplates()->orderBy('sort_order')->get();
        $categories = EventCategory::all();

        return view('admin.templates.budget', compact(
            'category',
            'templates',
            'categories'
        ));
    }

    public function store(Request $request, EventCategory $category)
    {
        $request->validate([
            'line_item'            => 'required|string|max:255',
            'suggested_percentage' => 'required|numeric|min:0|max:100',
            'scale_trigger'        => 'nullable|string|max:100',
        ]);

        $category->budgetTemplates()->create([
            'line_item'            => $request->line_item,
            'suggested_percentage' => $request->suggested_percentage,
            'scale_trigger'        => $request->scale_trigger ?? 'any',
            'sort_order'           => $category->budgetTemplates()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Budget item added.');
    }

    public function update(Request $request, EventCategory $category, BudgetTemplate $template)
    {
        $request->validate([
            'line_item'            => 'required|string|max:255',
            'suggested_percentage' => 'required|numeric|min:0|max:100',
            'scale_trigger'        => 'nullable|string|max:100',
        ]);

        $template->update($request->only([
            'line_item',
            'suggested_percentage',
            'scale_trigger',
        ]));

        return back()->with('success', 'Budget item updated.');
    }

    public function destroy(EventCategory $category, BudgetTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Budget item deleted.');
    }
}
