<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(CategoryRequest $request)
    {
        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category. Please try again.'
            ], 500);
        }
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        try {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category. Please try again.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        try {
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category. Please try again.'
            ], 500);
        }
    }
}
