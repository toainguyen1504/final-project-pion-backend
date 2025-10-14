<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        // get 10 categories/ 1 page, can change = query string: ?per_page=20
        $perPage = request()->get('per_page', 10);
        $categories = Category::latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'next_page_url' => $categories->nextPageUrl(),
                'prev_page_url' => $categories->previousPageUrl()
            ]
        ]);
    }


    public function store(CategoryRequest $request)
    {
        if (Category::where('name', $request->name)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Category name already exists.'
            ], 422);
        }


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

        if (Category::where('name', $request->name)->where('id', '!=', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Category name already exists.'
            ], 422);
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

        if ($category->posts()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with associated posts.'
            ], 409);
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
