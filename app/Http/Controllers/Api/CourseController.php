<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    // Lấy danh sách tất cả các khóa học
    public function index()
    {
        $perPage = request()->get('per_page', 10);
        $sort = request()->get('sort', 'updated_at');
        $order = request()->get('order', 'desc');
        $search = request()->get('search');

        $query = Course::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $courses = $query->orderBy($sort, $order)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
                'next_page_url' => $courses->nextPageUrl(),
                'prev_page_url' => $courses->previousPageUrl()
            ]
        ]);
    }

    // Tạo mới một khóa học
    public function store(CourseRequest $request)
    {
        $validated = $request->validated();
        $slug = $validated['slug'] ?? Str::slug($validated['title']);

        // Kiểm tra trùng slug trước khi insert
        if (Course::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!'
            ], 422);
        }

        $course = Course::create(array_merge($validated, ['slug' => $slug]));

        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được tạo thành công!',
            'data' => $course
        ], 201);
    }

    // Hiển thị chi tiết một khóa học
    public function show($id)
    {
        $course = Course::with(['program', 'category'])->find($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    // Cập nhật một khóa học
    public function update(CourseRequest $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại!'
            ], 404);
        }

        $validated = $request->validated();
        $slug = $validated['slug'] ?? Str::slug($validated['title'] ?? $course->title);

        // Kiểm tra trùng slug (ngoại trừ chính nó)
        if (Course::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!'
            ], 422);
        }

        $course->update(array_merge($validated, ['slug' => $slug]));

        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được cập nhật thành công!',
            'data' => $course
        ]);
    }

    // Xóa một khóa học
    public function destroy($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại!'
            ], 404);
        }

        try {
            $course->delete();
            return response()->json([
                'success' => true,
                'message' => 'Khóa học đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa khóa học. Vui lòng thử lại.'
            ], 500);
        }
    }

    // Xóa nhiều khóa học cùng lúc
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ID khóa học nào được cung cấp.'
            ], 400);
        }

        $courses = Course::whereIn('id', $ids)->get();

        if ($courses->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Một hoặc nhiều khóa học không tồn tại.'
            ], 404);
        }

        Course::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Các khóa học đã được xóa thành công.'
        ]);
    }
}
