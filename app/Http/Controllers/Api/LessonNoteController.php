<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LessonNote;

class LessonNoteController extends Controller
{
    /**
     *  Lấy danh sách note của 1 lesson
     */
    public function index(Request $request, $lessonId)
    {
        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        $order = $request->get('order', 'desc');
        $sortBy = $request->get('sort_by', 'created_at'); //sort flexible

        $notes = LessonNote::where('lesson_id', $lessonId)
            ->where('learner_id', $learner->id)
            ->with('lesson:id,title')
            ->orderBy($sortBy, $order)
            ->paginate(20);

        // transform res
        $notes->getCollection()->transform(function ($note) {
            return [
                'id' => $note->id,
                'content' => $note->content,
                'timestamp' => $note->timestamp,
                'lesson_id' => $note->lesson_id,
                'lesson_title' => $note->lesson->title ?? '---',
                'created_at' => $note->created_at,
                'updated_at' => $note->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $notes
        ]);
    }

    /**
     *  Lấy danh sách note theo course
     */
    public function getByCourse(Request $request, $courseId)
    {
        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        $order = $request->get('order', 'desc');
        $sortBy = $request->get('sort_by', 'created_at');

        $notes = LessonNote::where('learner_id', $learner->id)
            ->whereHas('lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->with('lesson:id,title')
            ->orderBy($sortBy, $order)
            ->paginate(20);

        $notes->getCollection()->transform(function ($note) {
            return [
                'id' => $note->id,
                'content' => $note->content,
                'timestamp' => $note->timestamp,
                'lesson_id' => $note->lesson_id,
                'lesson_title' => $note->lesson->title ?? '---',
                'created_at' => $note->created_at,
                'updated_at' => $note->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $notes
        ]);
    }

    /**
     *  Tạo note
     */
    public function store(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'content' => 'required|string',
            'timestamp' => 'required|integer|min:0',
        ]);

        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        // Optional: chống duplicate cùng timestamp
        $note = LessonNote::updateOrCreate(
            [
                'lesson_id' => $request->lesson_id,
                'learner_id' => $learner->id,
                'timestamp' => $request->timestamp,
            ],
            [
                'content' => $request->input('content')
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $note
        ]);
    }

    /**
     *  Update note
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        $note = LessonNote::where('id', $id)
            ->where('learner_id', $learner->id)
            ->firstOrFail();

        $note->update([
            'content' => $request->input('content')
        ]);

        return response()->json([
            'success' => true,
            'data' => $note
        ]);
    }

    /**
     *  Delete note
     */
    public function destroy(Request $request, $id)
    {
        $learner = $request->user()->learner;

        if (!$learner) {
            return response()->json([
                'success' => false,
                'message' => 'Learner not found'
            ], 404);
        }

        $note = LessonNote::where('id', $id)
            ->where('learner_id', $learner->id)
            ->firstOrFail();

        $note->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
