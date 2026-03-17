<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Str;

class CourseController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function paginationResponse($paginator)
    {
        return response()->json([
            'success' => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl()
            ]
        ]);
    }

    protected function checkEnrollment(Request $request, Course $course)
    {
        if (!$request->user()) {
            return false;
        }

        return $course->enrollments()
            ->where('user_id', $request->user()->id)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | CLIENT API
    |--------------------------------------------------------------------------
    */

    public function indexClient(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $courses = Course::where('status', 'published')
            ->withCount(['lessons as total_lessons'])
            ->withSum(['lessons as duration'], 'duration')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        $courses->getCollection()->transform(function ($course) use ($request) {

            $course->duration = $course->duration ?? 0;
            $course->enrolled = $this->checkEnrollment($request, $course);

            return $course;
        });

        return $this->paginationResponse($courses);
    }


    public function showClient(Request $request, $slug)
    {
        $course = Course::with([
            'program',
            'category',
            'lessons:id,title,slug,order,course_id,duration'
        ])
            ->withCount(['lessons as total_lessons'])
            ->withSum(['lessons as duration'], 'duration')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại hoặc chưa được công khai!'
            ], 404);
        }

        $course->duration = $course->duration ?? 0;
        $course->enrolled = $this->checkEnrollment($request, $course);

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }


    public function enroll(Request $request, $courseId)
    {
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

        $exists = $course->enrollments()
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đăng ký khóa học này rồi.'
            ], 422);
        }

        $course->enrollments()->create([
            'user_id' => $request->user()->id,
        ]);

        $course->increment('participants');

        $course->enrolled = true;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký khóa học thành công!',
            'data' => $course
        ]);
    }


    public function learningDetail(Request $request, $slug)
    {
        $course = Course::with([
            'program',
            'category',
            'lessons' => fn($q) => $q->orderBy('order')
        ])
            ->withCount(['lessons as total_lessons'])
            ->withSum(['lessons as duration'], 'duration')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại hoặc chưa được công khai!'
            ], 404);
        }

        $course->duration = $course->duration ?? 0;
        $course->enrolled = $this->checkEnrollment($request, $course);

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN CMS API
    |--------------------------------------------------------------------------
    */


    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort', 'updated_at');
        $order = $request->get('order', 'desc');
        $search = $request->get('search');
        $programId = $request->get('program_id');

        $query = Course::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($programId) {
            $query->where('program_id', $programId);
        }

        $courses = $query
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return $this->paginationResponse($courses);
    }


    public function store(CourseRequest $request)
    {
        $validated = $request->validated();

        $slug = $validated['slug'] ?? Str::slug($validated['title']);

        if (Course::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!'
            ], 422);
        }

        if ($request->filled('benefits')) {

            $benefits = preg_split("/\t+|\R/", $request->input('benefits'));

            $validated['benefits'] = array_values(
                array_filter(array_map('trim', $benefits))
            );
        }

        $course = Course::create([
            ...$validated,
            'slug' => $slug
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được tạo thành công!',
            'data' => $course
        ], 201);
    }


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

        $slug = $validated['slug']
            ?? Str::slug($validated['title'] ?? $course->title);

        if (
            Course::where('slug', $slug)
            ->where('id', '!=', $id)
            ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!'
            ], 422);
        }

        if ($request->filled('benefits')) {

            $benefits = preg_split("/\t+|\R/", $request->input('benefits'));

            $validated['benefits'] = array_values(
                array_filter(array_map('trim', $benefits))
            );
        }

        $course->update([
            ...$validated,
            'slug' => $slug
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được cập nhật thành công!',
            'data' => $course
        ]);
    }


    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại!'
            ], 404);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được xóa thành công!'
        ]);
    }


    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!$ids) {
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
