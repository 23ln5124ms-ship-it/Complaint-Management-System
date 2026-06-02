<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('complaints')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active'   => ['boolean'],
        ]);

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
            'color'       => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active'   => ['boolean'],
        ]);

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->complaints()->exists()) {
            return back()->with('error', 'Cannot delete category with existing complaints.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }
}
