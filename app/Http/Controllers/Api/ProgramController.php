<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use App\Http\Requests\ProgramRequest;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    // Lấy danh sách tất cả các chương trình học
    public function index()
    {
        $perPage = request()->get('per_page', 10);
        $sort = request()->get('sort', 'updated_at');
        $order = request()->get('order', 'desc');
        $search = request()->get('search');

        $query = Program::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $programs = $query->orderBy($sort, $order)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $programs->items(),
            'meta' => [
                'current_page' => $programs->currentPage(),
                'last_page' => $programs->lastPage(),
                'per_page' => $programs->perPage(),
                'total' => $programs->total(),
                'next_page_url' => $programs->nextPageUrl(),
                'prev_page_url' => $programs->previousPageUrl()
            ]
        ]);
    }

    // Tạo mới một chương trình học
    public function store(ProgramRequest $request)
    {
        $validated = $request->validated();

        $slug = $validated['slug'] ?? Str::slug($validated['title']);

        // Kiểm tra trùng slug trước khi insert
        if (Program::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!'
            ], 422);
        }

        $program = Program::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'user_id' => $validated['user_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chương trình học đã được tạo thành công!',
            'data' => $program
        ], 201);
    }


    // Hiển thị chi tiết một chương trình học
    public function show($id)
    {
        $program = Program::with('courses')->find($id);
        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Chương trình học không tồn tại!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $program
        ]);
    }

    // Cập nhật một chương trình học
    public function update(ProgramRequest $request, $id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Chương trình học không tồn tại!'
            ], 404);
        }

        $validated = $request->validated();
        $slug = $validated['slug'] ?? Str::slug($validated['title'] ?? $program->title);

        // Kiểm tra trùng slug (ngoại trừ chính nó)
        if (Program::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tiêu đề này đã tạo slug trùng, hãy đổi tiêu đề để tiếp tục!'
            ], 422);
        }

        $program->update([
            'title' => $validated['title'] ?? $program->title,
            'slug' => $slug,
            'description' => $validated['description'] ?? $program->description,
            'status' => $validated['status'] ?? $program->status,
            'user_id' => $validated['user_id'] ?? $program->user_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chương trình học đã được cập nhật thành công!',
            'data' => $program
        ]);
    }

    // Xóa một chương trình học
    public function destroy($id)
    {
        $program = Program::find($id);
        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Chương trình học không tồn tại!'
            ], 404);
        }

        try {
            $program->delete();
            return response()->json([
                'success' => true,
                'message' => 'Chương trình học đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa chương trình học. Vui lòng thử lại.'
            ], 500);
        }
    }

    // Xóa nhiều chương trình học cùng lúc
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ID chương trình nào được cung cấp.'
            ], 400);
        }

        $programs = Program::whereIn('id', $ids)->get();

        if ($programs->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Một hoặc nhiều chương trình không tồn tại.'
            ], 404);
        }

        Program::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Các chương trình học đã được xóa thành công.'
        ]);
    }
}
