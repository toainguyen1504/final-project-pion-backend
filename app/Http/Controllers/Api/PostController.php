<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostContent;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    public function index()
    {
        $perPage = request()->get('per_page', 10);
        $posts = Post::with(['category', 'content', 'categories'])->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl()
            ]
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

            $postData = [
                'title' => $request->title,
                'sapo_text' => $request->sapo_text,
                'user_id' => $request->user()->id,
                'category_id' => $mainCategoryId,
                'featured_media_id' => $request->input('featured_media_id'),
                'slug' => $request->slug ?? Str::slug($request->title),
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords' => $request->seo_keywords,
                'status' => $request->status,
                'visibility' => $request->visibility,
                'publish_at' => $request->publish_at,
            ];

            $post = Post::create($postData);

            PostContent::create([
                'post_id' => $post->id,
                'content_html' => $request->input('content'),
            ]);

            $post->categories()->sync($categoryIds);

            return response()->json([
                'status' => 'success',
                'message' => 'Post created successfully.',
                'data' => $post->fresh(['category', 'content', 'categories'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create post.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(PostRequest $request, $id)
    {

        try {
            $post = Post::with('content')->findOrFail($id);

            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null;

            // update data
            $updateData = [
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
            ];

            $post->update($updateData);

            // handle content
            $contentHtml = $request->input('content');

            if ($post->content) {
                $post->content->update([
                    'content_html' => $contentHtml
                ]);
            } else {
                PostContent::create([
                    'post_id' => $post->id,
                    'content_html' => $contentHtml
                ]);
            }

            $post->categories()->sync($categoryIds);

            return response()->json([
                'status' => 'success',
                'message' => 'Post updated successfully.',
                'data' => $post->fresh(['category', 'content', 'categories']) // fresh data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update post.',
                'error' => $e->getMessage() // debug
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
                'message' => 'Failed to delete post.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
