<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostContent;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['category', 'content', 'categories'])->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    public function show($id)
    {
        try {
            $post = Post::with(['category', 'content', 'categories'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $post
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ], 404);
        }
    }

    public function store(PostRequest $request)
    {
        try {
            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null;

            $post = Post::create([
                'title' => $request->title,
                'sapo_text' => $request->sapo_text,
                'user_id' => Auth::id(), // hoặc $request->user()->id nếu dùng Sanctum
                'category_id' => $mainCategoryId,
                'featured_media_id' => $request->input('featured_media_id'),
                'slug' => $request->slug ?? Str::slug($request->title),
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords' => $request->seo_keywords,
                'status' => $request->status,
                'visibility' => $request->visibility,
                'publish_at' => $request->publish_at,
            ]);

            PostContent::create([
                'post_id' => $post->id,
                'content_html' => $request->input('content'),
            ]);

            $post->categories()->sync($categoryIds);

            return response()->json([
                'status' => 'success',
                'message' => 'Post created successfully.',
                'data' => $post
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create post.'
            ], 500);
        }
    }

    public function update(PostRequest $request, $id)
    {
        try {
            $post = Post::with('content')->findOrFail($id);

            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null;

            $post->update([
                'title' => $request->title,
                'sapo_text' => $request->sapo_text,
                'category_id' => $mainCategoryId,
                'featured_media_id' => $request->input('featured_media_id'),
                'slug' => $request->slug ?? Str::slug($request->title),
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords' => $request->seo_keywords,
                'status' => $request->status,
                'visibility' => $request->visibility,
                'publish_at' => $request->publish_at,
            ]);

            $postContent = $post->content ?? new PostContent(['post_id' => $post->id]);
            $postContent->content_html = $request->input('content');
            $postContent->save();

            $post->categories()->sync($categoryIds);

            return response()->json([
                'status' => 'success',
                'message' => 'Post updated successfully.',
                'data' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update post.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::with('content')->findOrFail($id);

            if ($post->content) {
                $post->content->delete();
            }

            $post->categories()->detach();
            $post->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Post deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete post.'
            ], 500);
        }
    }
}
