<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Enrollment;

class LessonProgressController extends Controller
{
    /**
     * Update progress khi xem video
     */
    public function update(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'watched_duration' => 'required|integer|min:0'
        ]);

        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner profile not found'
            ], 404);
        }

        $lesson = Lesson::findOrFail($request->lesson_id);

        // seconds unit (s)
        $lessonDuration = $lesson->duration;

        // lấy progress cũ (để tránh spam update)
        $existing = LessonProgress::where('learner_id', $learner->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        // nếu time mới <= time cũ -> không update
        if ($existing && $request->watched_duration <= $existing->watched_duration) {
            return response()->json([
                'success' => true,
                'data' => $existing
            ]);
        }

        $isCompleted = $lessonDuration > 0
            ? $request->watched_duration >= ($lessonDuration * 0.9)
            : false; // 90% = completed

        $progress = LessonProgress::updateOrCreate(
            [
                'learner_id' => $learner->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'watched_duration' => $request->watched_duration,
                'last_watched_at' => now(),
                'is_completed' => $isCompleted
            ]
        );

        // Update progress khóa học
        $this->updateCourseProgress($learner->id, $lesson->course_id);

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Update progress course (cache vào enrollments)
     */
    protected function updateCourseProgress($learnerId, $courseId)
    {
        // lấy danh sách lesson_id trước -> tránh whereHas (nặng)
        $lessonIds = Lesson::where('course_id', $courseId)->pluck('id');

        $totalLessons = $lessonIds->count();

        if ($totalLessons === 0) return;

        $completedLessons = LessonProgress::where('learner_id', $learnerId)
            ->whereIn('lesson_id', $lessonIds)
            ->where('is_completed', true)
            ->count();

        $percent = round(($completedLessons / $totalLessons) * 100);

        Enrollment::where('learner_id', $learnerId)
            ->where('course_id', $courseId)
            ->update([
                'progress' => $percent
            ]);
    }

    /**
     * Danh sách khóa đang học (Home)
     */
    public function myLearningCourses(Request $request)
    {
        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        $courses = Enrollment::with('course:id,title,thumbnail')
            ->where('learner_id', $learner->id)
            ->get()
            ->map(function ($enrollment) {
                return [
                    'course_id' => $enrollment->course->id,
                    'title' => $enrollment->course->title,
                    'thumbnail' => $enrollment->course->thumbnail,
                    'progress' => $enrollment->progress,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     *  Khóa đang học gần nhất (Header)
     */
    public function currentLearning(Request $request)
    {
        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        $course = Enrollment::with('course:id,title,thumbnail')
            ->where('learner_id', $learner->id)
            ->orderByDesc('updated_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    /**
     *  Resume video
     */
    public function getLessonProgress(Request $request, $lessonId)
    {
        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        $progress = LessonProgress::where('learner_id', $learner->id)
            ->where('lesson_id', $lessonId)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }
}
