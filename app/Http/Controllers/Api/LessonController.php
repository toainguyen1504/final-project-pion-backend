<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    // Lấy danh sách tất cả các bài học
    public function index()
    {
        $perPage = request()->get('per_page', 10);
        $sort = request()->get('sort', 'updated_at');
        $order = request()->get('order', 'desc');
        $search = request()->get('search');
        $courseId = request()->get('course_id'); // lọc theo course

        $query = Lesson::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $lessons = $query->orderBy($sort, $order)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $lessons->items(),
            'meta' => [
                'current_page' => $lessons->currentPage(),
                'last_page' => $lessons->lastPage(),
                'per_page' => $lessons->perPage(),
                'total' => $lessons->total(),
                'next_page_url' => $lessons->nextPageUrl(),
                'prev_page_url' => $lessons->previousPageUrl()
            ]
        ]);
    }

    // Lấy tất cả bài học theo id của khóa học
    public function getByCourse($courseId)
    {
        $perPage = request()->get('per_page', 10);
        $sort = request()->get('sort', 'order'); // thường sắp xếp theo thứ tự bài học
        $order = request()->get('order', 'asc');

        $lessons = Lesson::with(['course.program']) // thêm course info
            ->where('course_id', $courseId)
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $lessons->items(),
            'meta' => [
                'current_page' => $lessons->currentPage(),
                'last_page' => $lessons->lastPage(),
                'per_page' => $lessons->perPage(),
                'total' => $lessons->total(),
                'next_page_url' => $lessons->nextPageUrl(),
                'prev_page_url' => $lessons->previousPageUrl()
            ]
        ]);
    }

    // Tạo mới một bài học
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:lessons,slug',
            'intro' => 'nullable|string',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'video_url' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'is_preview' => 'boolean',
            'is_quiz' => 'boolean',
            'course_id' => 'required|exists:courses,id',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['title']);

        if (Lesson::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Slug đã tồn tại, hãy đổi tiêu đề hoặc slug!'
            ], 422);
        }

        $lesson = Lesson::create(array_merge($validated, ['slug' => $slug]));

        return response()->json([
            'success' => true,
            'message' => 'Bài học đã được tạo thành công!',
            'data' => $lesson
        ], 201);
    }

    // Hiển thị chi tiết một bài học
    public function show($id)
    {
        $lesson = Lesson::with('course')->find($id);
        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lesson
        ]);
    }

    // Cập nhật một bài học
    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại!'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:lessons,slug,' . $id,
            'intro' => 'nullable|string',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'video_url' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'is_preview' => 'boolean',
            'is_quiz' => 'boolean',
            'course_id' => 'required|exists:courses,id',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['title'] ?? $lesson->title);

        $lesson->update(array_merge($validated, ['slug' => $slug]));

        return response()->json([
            'success' => true,
            'message' => 'Bài học đã được cập nhật thành công!',
            'data' => $lesson
        ]);
    }

    // Xóa một bài học
    public function destroy($id)
    {
        $lesson = Lesson::find($id);
        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại!'
            ], 404);
        }

        try {
            $lesson->delete();
            return response()->json([
                'success' => true,
                'message' => 'Bài học đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa bài học. Vui lòng thử lại.'
            ], 500);
        }
    }

    // Xóa nhiều bài học cùng lúc
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ID bài học nào được cung cấp.'
            ], 400);
        }

        $lessons = Lesson::whereIn('id', $ids)->get();

        if ($lessons->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Một hoặc nhiều bài học không tồn tại.'
            ], 404);
        }

        Lesson::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Các bài học đã được xóa thành công.'
        ]);
    }
}
