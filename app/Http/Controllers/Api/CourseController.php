<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    // Lấy danh sách khóa học cho client
    public function indexClient(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $courses = Course::where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        // Tính lại duration và total_lessons cho từng course
        $courses->getCollection()->transform(function ($course) use ($request) {
            $course->total_lessons = $course->lessons()->count();
            $course->duration = $course->lessons()->sum('duration');

            // gắn flag enrolled cho từng course (nếu user đã login) -> để nếu có -> thay vì chuyển sang detail -> thì đẩy thẳng qua learning mode luôn
            $course->enrolled = false;
            if ($request->user()) {
                $course->enrolled = $course->enrollments()->where('user_id', $request->user()->id)->exists();
            }

            return $course;
        });


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

    // Hiển thị chi tiết một khóa học cho client
    public function showClient(Request $request, $slug)
    {
        $course = Course::with([
            'program',
            'category',
            'lessons' => function ($query) {
                $query->select('id', 'title', 'slug', 'order', 'course_id', 'duration');
            }
        ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại hoặc chưa được công khai!'
            ], 404);
        }

        // Tính lại duration và total_lessons
        $course->total_lessons = $course->lessons->count();
        $course->duration = $course->lessons->sum('duration');
        // participants xử lý sau

        // thêm flag enrolled 
        $enrolled = false;
        if ($request->user()) {
            $enrolled = $course->enrollments()->where('user_id', $request->user()->id)->exists();
        }
        $course->enrolled = $enrolled;

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    // Đăng ký khóa học (enroll) - chỉ dành cho learner đã đăng nhập
    public function enroll(Request $request, $courseId)
    {
        // Kiểm tra user đã đăng nhập
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để đăng ký khóa học.'
            ], 401);
        }

        $course = Course::find($courseId);
        if (!$course || $course->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại hoặc chưa được công khai!'
            ], 404);
        }

        // Kiểm tra user đã đăng ký chưa
        $exists = $course->enrollments()->where('user_id', $request->user()->id)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đăng ký khóa học này rồi.'
            ], 422);
        }

        // Tạo enrollment mới
        $course->enrollments()->create([
            'user_id' => $request->user()->id,
        ]);

        // Cập nhật participants (đếm lại số lượng enrollments)
        $course->participants = $course->enrollments()->count();
        $course->save();

        // thêm flag enrolled cho user hiện tại 
        $course->enrolled = true;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký khóa học thành công!',
            'data' => $course
        ]);
    }

    // learning mode
    public function learningDetail(Request $request, $slug)
    {
        $course = Course::with([
            'program',
            'category',
            'lessons' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại hoặc chưa được công khai!'
            ], 404);
        }

        // Tính lại duration và total_lessons
        $course->total_lessons = $course->lessons->count();
        $course->duration = $course->lessons->sum('duration');

        // Flag enrolled cho user hiện tại
        $enrolled = false;
        if ($request->user()) {
            $enrolled = $course->enrollments()->where('user_id', $request->user()->id)->exists();
        }
        $course->enrolled = $enrolled;

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    // -----------------------------
    //  START - Admin routes cho CMS
    // -----------------------------
    // Lấy danh sách tất cả các khóa học
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort', 'updated_at');
        $order = $request->get('order', 'desc');
        $search = $request->get('search');
        $programId = $request->get('program_id'); // lấy program_id từ query

        $query = Course::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($programId) {
            $query->where('program_id', $programId); // lọc theo program_id
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

        // Parse textarea thành mảng 
        if ($request->filled('benefits')) {
            $validated['benefits'] = preg_split("/\t|\r\n|\n/", $request->input('benefits'), -1, PREG_SPLIT_NO_EMPTY);
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

        // Parse textarea thành mảng 
        if ($request->has('benefits')) {
            $validated['benefits'] = preg_split("/\t|\r\n|\n/", $request->input('benefits'), -1, PREG_SPLIT_NO_EMPTY);
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
