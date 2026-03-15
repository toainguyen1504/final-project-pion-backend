<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use Illuminate\Http\Request;
use App\Http\Requests\FlashcardRequest;

class FlashcardController extends Controller
{
    // Lấy danh sách tất cả flashcards
    public function index(Request $request)
    {
        $perPage   = $request->get('per_page', 10);
        $sort      = $request->get('sort', 'order');
        $order     = $request->get('order', 'asc');
        $search    = $request->get('search');
        $lessonId  = $request->get('lesson_id');
        $courseId  = $request->get('course_id');
        $programId = $request->get('program_id');

        // whitelist các field cho phép sort
        $allowedSorts = ['id', 'vocabulary', 'translation', 'order', 'level'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'order';
        }

        $query = Flashcard::query()
            ->with(['lesson:id,title,course_id', 'lesson.course:id,title,program_id', 'lesson.course.program:id,title']);

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('vocabulary', 'like', "%{$search}%")
                    ->orWhere('translation', 'like', "%{$search}%");
            });
        }

        // Filter lesson
        if ($lessonId) {
            $query->where('lesson_id', $lessonId);
        }

        // Filter course
        if ($courseId) {
            $query->whereHas('lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        // Filter program
        if ($programId) {
            $query->whereHas('lesson.course', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }

        $flashcards = $query
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $flashcards->items(),
            'meta' => [
                'current_page'   => $flashcards->currentPage(),
                'last_page'      => $flashcards->lastPage(),
                'per_page'       => $flashcards->perPage(),
                'total'          => $flashcards->total(),
                'next_page_url'  => $flashcards->nextPageUrl(),
                'prev_page_url'  => $flashcards->previousPageUrl(),
            ]
        ]);
    }

    // Tạo mới flashcard
    public function store(FlashcardRequest  $request)
    {
        $validated = $request->validated();

        $flashcard = Flashcard::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Flashcard đã được tạo thành công!',
            'data' => $flashcard
        ], 201);
    }

    // Tạo nhiều flashcard từ text input (POST /flashcards/bulk)
    public function bulkStore(Request $request)
    {
        $lessonId = $request->input('lesson_id');
        $rawText  = $request->input('text'); // toàn bộ input từ người dùng

        if (!$lessonId || !$rawText) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu lesson_id hoặc text input.'
            ], 400);
        }

        // Mỗi dòng là một flashcard, các field cách nhau bằng tab
        // Format: vocabulary \t phonetic \t translation \t example_sentence \t example_translation
        $lines   = preg_split("/\r\n|\n|\r/", trim($rawText));
        $created = [];

        foreach ($lines as $index => $line) {
            $parts = explode("\t", $line);

            if (count($parts) < 4) {
                // bỏ qua dòng không hợp lệ (ít hơn 4 field)
                continue;
            }

            $vocabulary          = trim($parts[0]);
            $phonetic            = trim($parts[1]);
            $translation         = trim($parts[2]);
            $example_sentence    = trim($parts[3]);
            $example_translation = $parts[4] ?? null; // nếu có thêm cột thứ 5

            if ($vocabulary && $translation) {
                $flashcard = Flashcard::create([
                    'vocabulary'          => $vocabulary,
                    'phonetic'            => $phonetic,
                    'translation'         => $translation,
                    'example_sentence'    => $example_sentence,
                    'example_translation' => $example_translation,
                    'lesson_id'           => $lessonId,
                    'order'               => $index + 1,
                ]);
                $created[] = $flashcard;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo ' . count($created) . ' flashcard từ input.',
            'data'    => $created
        ], 201);
    }


    // Hiển thị chi tiết flashcard
    public function show($id)
    {
        $flashcard = Flashcard::with('lesson')->find($id);
        if (!$flashcard) {
            return response()->json([
                'success' => false,
                'message' => 'Flashcard không tồn tại!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $flashcard
        ]);
    }

    // Cập nhật flashcard
    public function update(FlashcardRequest $request, $id)
    {
        $flashcard = Flashcard::find($id);
        if (!$flashcard) {
            return response()->json([
                'success' => false,
                'message' => 'Flashcard không tồn tại!'
            ], 404);
        }

        $validated = $request->validated();

        $flashcard->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Flashcard đã được cập nhật thành công!',
            'data' => $flashcard
        ]);
    }

    // Xóa flashcard
    public function destroy($id)
    {
        $flashcard = Flashcard::find($id);
        if (!$flashcard) {
            return response()->json([
                'success' => false,
                'message' => 'Flashcard không tồn tại!'
            ], 404);
        }

        try {
            $flashcard->delete();
            return response()->json([
                'success' => true,
                'message' => 'Flashcard đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa flashcard. Vui lòng thử lại.'
            ], 500);
        }
    }

    // Xóa nhiều flashcard cùng lúc
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ID flashcard nào được cung cấp.'
            ], 400);
        }

        $flashcards = Flashcard::whereIn('id', $ids)->get();

        if ($flashcards->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Một hoặc nhiều flashcard không tồn tại.'
            ], 404);
        }

        Flashcard::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Các flashcard đã được xóa thành công.'
        ]);
    }

    // Lấy tất cả flashcard theo id của bài học
    public function getByLesson($lessonId)
    {
        $perPage = request()->get('per_page', 10);
        $sort = request()->get('sort', 'order');
        $order = request()->get('order', 'asc');

        $flashcards = Flashcard::where('lesson_id', $lessonId)
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $flashcards->items(),
            'meta' => [
                'current_page' => $flashcards->currentPage(),
                'last_page' => $flashcards->lastPage(),
                'per_page' => $flashcards->perPage(),
                'total' => $flashcards->total(),
                'next_page_url' => $flashcards->nextPageUrl(),
                'prev_page_url' => $flashcards->previousPageUrl()
            ]
        ]);
    }
}
