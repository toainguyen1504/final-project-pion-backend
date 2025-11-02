<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostContent;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    public function index()
    {
        // Tự động cập nhật scheduled_public -> public khi đến giờ
        $now = now();
        Post::where('visibility', 'scheduled_public')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', $now)
            ->update(['visibility' => 'public']);

        // Pagination & filters
        $perPage = request()->get('per_page', 10);
        $sort = request()->get('sort', 'publish_at');
        $order = request()->get('order', 'desc');
        $search = request()->get('search');

        $query = Post::with(['category', 'content', 'categories']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('seo_title', 'like', "%{$search}%")
                    ->orWhere('seo_keywords', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy($sort, $order)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl(),
            ]
        ]);
    }

    public function show($id)
    {
        $post = Post::with(['category', 'content', 'categories'])->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    }

    public function store(PostRequest $request)
    {
        try {
            // Avoid duplicate titles
            if (Post::where('title', $request->title)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post title already exists.'
                ], 422);
            }

            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null;

            $post = Post::create([
                'title' => $request->title,
                'sapo_text' => $request->sapo_text,
                'slug' => $request->slug ?? Str::slug($request->title),
                'user_id' => $request->user()->id ?? 1, // fallback nếu chưa có auth
                'category_id' => $mainCategoryId,
                'featured_media_id' => $request->input('featured_media_id'),
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords' => $request->seo_keywords,
                'status' => $request->status ?? 'draft',
                'visibility' => $request->visibility ?? 'private',
                'publish_at' => $request->publish_at,
            ]);

            // create content
            PostContent::create([
                'post_id' => $post->id,
                'content_html' => $request->input('content')
            ]);

            // assign categories
            $post->categories()->sync($categoryIds);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully!',
                'data' => $post->fresh(['category', 'content', 'categories'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(PostRequest $request, $id)
    {
        $post = Post::with('content')->find($id);
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found.'
            ], 404);
        }

        // check title duplication
        if (Post::where('title', $request->title)->where('id', '!=', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Post title already exists.'
            ], 422);
        }

        try {
            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null;

            // Logic auto điều chỉnh visibility dựa vào publish_at
            $now = now();
            $publishAt = $request->publish_at;

            if ($publishAt && $publishAt > $now) {
                $request->merge(['visibility' => 'scheduled_public']);
            } elseif ($publishAt && $publishAt <= $now) {
                $request->merge(['visibility' => 'public']);
            }

            $post->update([
                'title' => $request->title,
                'sapo_text' => $request->sapo_text,
                'slug' => $request->slug ?? Str::slug($request->title),
                'category_id' => $mainCategoryId,
                'featured_media_id' => $request->input('featured_media_id'),
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords' => $request->seo_keywords,
                'status' => $request->status,
                'visibility' => $request->visibility,
                'publish_at' => $request->publish_at,
            ]);

            // Update or create content
            $contentHtml = $request->input('content');
            if ($post->content) {
                $post->content->update(['content_html' => $contentHtml]);
            } else {
                PostContent::create([
                    'post_id' => $post->id,
                    'content_html' => $contentHtml
                ]);
            }

            // sync categories
            $post->categories()->sync($categoryIds);

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully!',
                'data' => $post->fresh(['category', 'content', 'categories'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $post = Post::with('content')->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found.'
            ], 404);
        }

        try {
            if ($post->content) {
                $post->content->delete();
            }

            $post->categories()->detach();
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No post IDs provided.'
            ], 400);
        }

        $posts = Post::whereIn('id', $ids)->get();

        if ($posts->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'One or more posts not found.'
            ], 404);
        }

        try {
            foreach ($posts as $post) {
                if ($post->content) {
                    $post->content->delete();
                }
                $post->categories()->detach();
            }

            Post::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posts deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete posts.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Stats hiển thị cho dashboard (admin cms)
    public function stats(Request $request)
    {
        $field = $request->get('field', 'created_at');

        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();

        $lastMonthStart = $thisMonthStart->copy()->subMonth();
        $lastMonthEnd = $thisMonthStart->copy()->subSecond();

        $thisMonthCount = Post::whereBetween($field, [$thisMonthStart, $thisMonthEnd])->count();
        $lastMonthCount = Post::whereBetween($field, [$lastMonthStart, $lastMonthEnd])->count();
        $totalCount = Post::count();

        // dd($totalCount, $thisMonthCount, $lastMonthCount);

        return response()->json([
            'success' => true,
            'data' => [
                'this_month' => $thisMonthCount,
                'last_month' => $lastMonthCount,
                'total' => $totalCount,
            ]
        ]);
    }
}
