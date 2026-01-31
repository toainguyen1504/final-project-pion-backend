<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        // get 10 categories/ 1 page, can change = query string: ?per_page=20
        $perPage = request()->get('per_page', 10);

        // sort by updated_at
        $sort = request()->get('sort', 'updated_at');
        $order = request()->get('order', 'desc');

        // search
        $search = request()->get('search');
        $query = Category::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy($sort, $order)->paginate($perPage);

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
                'message' => 'Danh mục đã tồn tại!'
            ], 422);
        }


        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'type' => $request->input('type', 'post'),
                'is_featured' => $request->boolean('is_featured', false),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được tạo thành công!',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo danh mục. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Danh mục không tồn tại!'
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
                'message' => 'Danh mục không tồn tại!'
            ], 404);
        }

        // Kiểm tra xem category name có thay đổi không
        if ($category->name !== $request->input('name')) {
            // Nếu tên thay đổi, cần kiểm tra lại unique
            $request->validate([
                'name' => 'unique:categories,name,' . $id
            ]);
        }

        if (Category::where('name', $request->name)->where('id', '!=', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tên danh mục đã tồn tại!'
            ], 422);
        }

        // Cập nhật các trường khác của category
        try {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'type' => $request->input('type', $category->type),
                'is_featured' => $request->boolean('is_featured', $category->is_featured),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được cập nhật thành công!',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật danh mục. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Danh mục không tồn tại!'
            ], 404);
        }

        if ($category->posts()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục có bài viết liên quan.'
            ], 409);
        }

        try {
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Danh mục đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ID danh mục nào được cung cấp.'
            ], 400);
        }

        $categories = Category::whereIn('id', $ids)->get();

        // Check if all requested IDs exist
        if ($categories->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Một hoặc nhiều danh mục không tồn tại.'
            ], 404);
        }

        // Check if any category is linked to posts
        foreach ($categories as $category) {
            if ($category->posts()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa danh mục có bài viết liên quan.'
                ], 409);
            }
        }

        // Proceed to delete
        Category::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Danh mục đã được xóa thành công.'
        ]);
    }

    // Stats hiển thị cho dashboard (admin cms)
    public function stats(Request $request)
    {
        $field = $request->get('field', 'created_at');

        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();

        $lastMonthStart = $thisMonthStart->copy()->subMonth();
        $lastMonthEnd = $thisMonthStart->copy()->subSecond();

        $thisMonthCount = Category::whereBetween($field, [$thisMonthStart, $thisMonthEnd])->count();
        $lastMonthCount = Category::whereBetween($field, [$lastMonthStart, $lastMonthEnd])->count();
        $totalCount = Category::count();

        // dd($totalCount, $thisMonthCount, $lastMonthCount);

        return response()->json([
            'success' => true,
            'data' => [
                'this_month' => $thisMonthCount,
                'last_month' => $lastMonthCount,
                'total' => $totalCount,
            ]
        ]);
    }
}
