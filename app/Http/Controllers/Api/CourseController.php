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
            'data' => collect($paginator->items())->map(fn($course) => $this->transformCourse($course)),
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
        if (!$request->user() || !$request->user()->learner) {
            return false;
        }

        $learner = $request->user()->learner;

        return $course->enrollments()
            ->where('learner_id', $learner->id)
            ->exists();
    }

    protected function normalizeBenefits($benefits): array|null
    {
        if (is_array($benefits)) {
            return array_values(array_filter(array_map('trim', $benefits)));
        }

        if (is_string($benefits) && trim($benefits) !== '') {
            return array_values(array_filter(array_map(
                'trim',
                preg_split("/\t+|\R/", $benefits)
            )));
        }

        return null;
    }

    protected function transformCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'language' => $course->language,
            'thumbnail' => $course->thumbnail,
            'thumbnail_media_id' => $course->thumbnail_media_id,
            'thumbnail_url' => $course->thumbnail_url,
            'thumbnail_thumb' => $course->thumbnail_thumb,
            'thumbnail_og' => $course->thumbnail_og,
            'thumbnail_media' => $course->thumbnailMedia ? [
                'id' => $course->thumbnailMedia->id,
                'title' => $course->thumbnailMedia->title,
                'path' => $course->thumbnailMedia->path,
                'mime_type' => $course->thumbnailMedia->mime_type,
                'type' => $course->thumbnailMedia->type,
                'meta' => $course->thumbnailMedia->meta,
                'url' => asset($course->thumbnailMedia->getVariantPath('medium')),
            ] : null,
            'description' => $course->description,
            'price' => $course->price,
            'discount_price' => $course->discount_price,
            'level' => $course->level,
            'status' => $course->status,
            'duration' => $course->duration ?? 0,
            'participants' => $course->participants,
            'total_lessons' => $course->total_lessons ?? 0,
            'benefits' => $course->benefits,
            'is_free' => $course->is_free,
            'program_id' => $course->program_id,
            'category_id' => $course->category_id,
            'user_id' => $course->user_id,
            'program' => $course->program ?? null,
            'category' => $course->category ?? null,
            'lessons' => $course->lessons ?? null,
            'enrolled' => $course->enrolled ?? false,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CLIENT API
    |--------------------------------------------------------------------------
    */

    public function indexClient(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $courses = Course::with(['thumbnailMedia'])
            ->where('status', 'published')
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
            'thumbnailMedia',
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
            'data' => $this->transformCourse($course)
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

        $course = Course::with('thumbnailMedia')->find($courseId);

        if (!$course || $course->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại hoặc chưa được công khai!'
            ], 404);
        }

        if (!$request->user()->learner) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn chưa có hồ sơ học viên.'
            ], 422);
        }

        $learner = $request->user()->learner;

        $exists = $course->enrollments()
            ->where('learner_id', $learner->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đăng ký khóa học này rồi.'
            ], 422);
        }

        $course->enrollments()->create([
            'learner_id' => $learner->id,
        ]);

        $course->increment('participants');
        $course->refresh();

        $course->enrolled = true;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký khóa học thành công!',
            'data' => $this->transformCourse($course)
        ]);
    }

    public function learningDetail(Request $request, $slug)
    {
        $course = Course::with([
            'program',
            'category',
            'thumbnailMedia',
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
            'data' => $this->transformCourse($course)
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

        // $query = Course::with(['thumbnailMedia', 'program', 'category']);
        $query = Course::with(['thumbnailMedia', 'program', 'category'])
            ->withCount(['lessons as total_lessons'])
            ->withSum(['lessons as duration'], 'duration');

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

        $validated['benefits'] = $this->normalizeBenefits($request->input('benefits'));
        $validated['slug'] = $slug;

        $course = Course::create($validated);
        $course->load(['thumbnailMedia', 'program', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được tạo thành công!',
            'data' => $this->transformCourse($course)
        ], 201);
    }

    public function show($id)
    {
        // $course = Course::with(['program', 'category', 'thumbnailMedia'])->find($id);
        $course = Course::with(['program', 'category', 'thumbnailMedia', 'lessons'])
            ->withCount(['lessons as total_lessons'])
            ->withSum(['lessons as duration'], 'duration')
            ->find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformCourse($course)
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

        $slug = $validated['slug'] ?? Str::slug($validated['title'] ?? $course->title);

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

        $validated['benefits'] = $this->normalizeBenefits($request->input('benefits'));
        $validated['slug'] = $slug;

        $course->update($validated);
        $course->load(['thumbnailMedia', 'program', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Khóa học đã được cập nhật thành công!',
            'data' => $this->transformCourse($course)
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
