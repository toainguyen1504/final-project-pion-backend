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
    // Hàm chung để build query
    private function buildQuery(Request $request, bool $onlyPublic = false)
    {
        $perPage = $request->get('per_page', 10);
        $sort    = $request->get('sort', 'publish_at');
        $order   = $request->get('order', 'desc');
        $search  = $request->get('search');

        $query = Post::with(['category', 'content', 'categories']);

        if ($onlyPublic) {
            $query->where('visibility', 'public');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('seo_title', 'like', "%{$search}%")
                    ->orWhere('seo_keywords', 'like', "%{$search}%");
            });
        }

        return $query->orderBy($sort, $order)->paginate($perPage);
    }

    // Auto update scheduled -> public
    private function autoUpdateVisibility()
    {
        $now = now();
        Post::where('visibility', 'scheduled_public')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', $now)
            ->update(['visibility' => 'public']);
    }


    // Hàm chung format response
    private function formatResponse($posts)
    {
        return response()->json([
            'success' => true,
            'data'    => $posts->items(),
            'meta'    => [
                'current_page'  => $posts->currentPage(),
                'last_page'     => $posts->lastPage(),
                'per_page'      => $posts->perPage(),
                'total'         => $posts->total(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl(),
            ]
        ]);
    }

    // Hàm chung lấy detail cho client site và admin cms 
    private function getPostDetail(Request $request, $id, bool $onlyPublic = false)
    {
        $this->autoUpdateVisibility();

        $query = Post::with(['category', 'content', 'categories']);

        if ($onlyPublic) {
            $query->where('visibility', 'public');
        }

        $post = $query->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post không tồn tại!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $post
        ]);
    }

    // Client: API hiển thị danh sách Guest (chỉ public) - this is for frontend (client site)
    public function indexClient(Request $request)
    {
        $this->autoUpdateVisibility();

        $posts = $this->buildQuery($request, true);

        return $this->formatResponse($posts);
    }

    public function showClient(Request $request, $id)
    {
        return $this->getPostDetail($request, $id, true);
    }

    // CMS: API hiển thị danh sách cho Admin CMS (tất cả)
    public function index(Request $request)
    {
        $this->autoUpdateVisibility();

        // Admin có thể xem tất cả, không filter
        $posts = $this->buildQuery($request, false);

        return $this->formatResponse($posts);
    }

    // detail admin cms
    public function show(Request $request, $id)
    {
        return $this->getPostDetail($request, $id, false);
    }

    public function store(PostRequest $request)
    {
        try {
            // Avoid duplicate titles or slugs
            if (Post::where('title', $request->title)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiêu đề bài viết đã tồn tại!'
                ], 422);
            }

            $slug = $request->slug ?? Str::slug($request->title);
            if (Post::where('slug', $slug)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đường dẫn (Slug) của bài viết đã tồn tại!'
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
                'message' => 'Bài viết đã được tạo thành công.',
                'data' => $post->fresh(['category', 'content', 'categories'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo bài viết!',
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
                'message' => 'Bài viết không tồn tại!'
            ], 404);
        }

        // Avoid duplicate titles or slugs
        if (Post::where('title', $request->title)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tiêu đề bài viết đã tồn tại!'
            ], 422);
        }

        $slug = $request->slug ?? Str::slug($request->title);
        if (Post::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Đường dẫn (Slug) của bài viết đã tồn tại!'
            ], 422);
        }

        try {
            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null;

            // Logic auto điều chỉnh visibility chỉ áp dụng khi user muốn public
            $now = now();
            $publishAt = $request->publish_at;

            // Nếu người dùng chọn public → kiểm tra xem có đặt lịch hay không
            if ($request->visibility === 'public') {
                if ($publishAt && $publishAt > $now) {
                    $request->merge(['visibility' => 'scheduled_public']);
                } elseif ($publishAt && $publishAt <= $now) {
                    $request->merge(['visibility' => 'public']);
                }
            }

            $post->update([
                'title' => $request->title,
                'sapo_text' => $request->sapo_text,
                'slug' => $request->filled('slug') ? $request->slug : $post->slug,
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
                'message' => 'Cập nhật bài viết thành công.',
                'data' => $post->fresh(['category', 'content', 'categories'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật bài viết!',
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
                'message' => 'Bài viết đã được xóa thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa bài viết!',
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
                'message' => 'Không có ID bài viết nào được cung cấp.'
            ], 400);
        }

        $posts = Post::whereIn('id', $ids)->get();

        if ($posts->count() !== count($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Một hoặc nhiều bài viết không tồn tại.'
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
                'message' => 'Xóa các bài viết thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa bài viết.',
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
