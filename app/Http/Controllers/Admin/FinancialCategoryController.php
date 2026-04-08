<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialCategory;
use Illuminate\Http\Request;

class FinancialCategoryController extends Controller
{
    public function index()
    {
        $categories = FinancialCategory::withCount('expenses')->latest()->get();
        return view('admin.financial-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:financial_categories,name',
            'type'        => 'required|in:revenue,expense',
            'description' => 'nullable|string|max:255',
        ]);
        FinancialCategory::create($data);
        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, FinancialCategory $financialCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:financial_categories,name,' . $financialCategory->id,
            'description' => 'nullable|string|max:255',
        ]);
        $financialCategory->update($data);
        return back()->with('success', 'Category updated.');
    }

    public function destroy(FinancialCategory $financialCategory)
    {
        if ($financialCategory->is_system) {
            return back()->with('error', 'System categories cannot be deleted.');
        }
        if ($financialCategory->expenses()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing expenses.');
        }
        $financialCategory->delete();
        return back()->with('success', 'Category deleted.');
    }
}
