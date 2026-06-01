<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController
{
    public function index()
    {
        $categories = Category::where('is_active', true)
                              ->withCount('complaints')
                              ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function show(Category $category)
    {
        $category->loadCount('complaints');

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    public function store(Request $request)
    {
        // Only admins can create categories
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:categories|max:255',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|regex:/^#[0-9A-F]{6}$/i',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:categories,name,' . $category->id . '|max:255',
            'description' => 'sometimes|string|max:1000',
            'color' => 'sometimes|regex:/^#[0-9A-F]{6}$/i',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($category->complaints()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with complaints',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
