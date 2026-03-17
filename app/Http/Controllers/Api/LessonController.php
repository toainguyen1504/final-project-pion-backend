<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\YoutubeService;

class LessonController extends Controller
{
    protected YoutubeService $youtubeService;

    public function __construct(YoutubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    /*
    |--------------------------------------------------------------------------
    | Query builder helper
    |--------------------------------------------------------------------------
    */

    protected function lessonQuery(Request $request)
    {
        $query = Lesson::query()->with('course.program');

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }

        if ($programId = $request->get('program_id')) {
            $query->whereHas(
                'course.program',
                fn($q) =>
                $q->where('id', $programId)
            );
        }

        return $query;
    }

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

    protected function processYoutube(array &$validated)
    {
        if (empty($validated['video_url'])) {
            return;
        }

        $validated['video_url'] =
            $this->youtubeService->convertYoutubeUrl($validated['video_url']);

        $duration =
            $this->youtubeService->getDurationFromUrl($validated['video_url']);

        if ($duration) {
            $validated['duration'] = $duration;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort', 'updated_at');
        $order = $request->get('order', 'desc');

        $lessons = $this->lessonQuery($request)
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return $this->paginationResponse($lessons);
    }

    /*
    |--------------------------------------------------------------------------
    | Lessons by Course
    |--------------------------------------------------------------------------
    */

    public function getByCourse($courseId)
    {
        $perPage = request()->get('per_page', 10);

        $lessons = Lesson::with('course.program')
            ->where('course_id', $courseId)
            ->orderBy('order')
            ->paginate($perPage);

        return $this->paginationResponse($lessons);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $this->validateLesson($request);

        return DB::transaction(function () use (&$validated) {

            $validated['slug'] =
                $validated['slug'] ?? Str::slug($validated['title']);

            $this->processYoutube($validated);

            $courseId = $validated['course_id'];

            $maxOrder = Lesson::where('course_id', $courseId)->max('order') ?? 0;

            $order = $validated['order'] ?? $maxOrder + 1;

            Lesson::where('course_id', $courseId)
                ->where('order', '>=', $order)
                ->increment('order');

            $lesson = Lesson::create([
                ...$validated,
                'order' => $order
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bài học đã được tạo thành công!',
                'data' => $lesson
            ], 201);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại!'
            ], 404);
        }

        $validated = $this->validateLesson($request, $id);

        return DB::transaction(function () use ($lesson, &$validated) {

            $validated['slug'] =
                $validated['slug'] ?? Str::slug($validated['title']);

            $this->processYoutube($validated);

            $newOrder = $validated['order'] ?? $lesson->order;
            $oldOrder = $lesson->order;
            $courseId = $lesson->course_id;

            if ($newOrder > $oldOrder) {
                Lesson::where('course_id', $courseId)
                    ->whereBetween('order', [$oldOrder + 1, $newOrder])
                    ->decrement('order');
            } elseif ($newOrder < $oldOrder) {
                Lesson::where('course_id', $courseId)
                    ->whereBetween('order', [$newOrder, $oldOrder - 1])
                    ->increment('order');
            }

            $lesson->update([
                ...$validated,
                'order' => $newOrder
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bài học đã được cập nhật thành công!',
                'data' => $lesson
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại!'
            ], 404);
        }

        return DB::transaction(function () use ($lesson) {

            $courseId = $lesson->course_id;
            $order = $lesson->order;

            $lesson->delete();

            Lesson::where('course_id', $courseId)
                ->where('order', '>', $order)
                ->decrement('order');

            return response()->json([
                'success' => true,
                'message' => 'Bài học đã được xóa thành công!'
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk destroy
    |--------------------------------------------------------------------------
    */

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!$ids) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ID bài học nào.'
            ], 400);
        }

        return DB::transaction(function () use ($ids) {

            $lessons = Lesson::whereIn('id', $ids)->get();

            if ($lessons->count() !== count($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Một số bài học không tồn tại.'
                ], 404);
            }

            $courseId = $lessons->first()->course_id;

            Lesson::whereIn('id', $ids)->delete();

            $remaining = Lesson::where('course_id', $courseId)
                ->orderBy('order')
                ->get();

            foreach ($remaining as $index => $lesson) {
                $lesson->update([
                    'order' => $index + 1
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Các bài học đã được xóa thành công.'
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function validateLesson(Request $request, $id = null)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:lessons,slug,' . $id,
            'intro' => 'nullable|string',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'video_url' => 'nullable|string',
            'order' => 'nullable|numeric|min:1',
            'is_preview' => 'boolean',
            'is_quiz' => 'boolean',
            'course_id' => 'required|exists:courses,id',
        ]);
    }
}
