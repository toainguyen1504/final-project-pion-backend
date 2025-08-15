<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function index()
    {
        $posts = Post::with('category')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    public function show($id)
    {
        try {
            $posts = Post::with('category')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $posts
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        }
    }
}
