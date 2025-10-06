<?php

namespace App\Http\Controllers\Admin;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Media;
use App\Models\Category;
use App\Models\Template;
use App\Models\PostContent;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['content', 'categories'])->latest()->paginate(10);
        return view('pages.admin.posts.index', compact('posts'));
    }

    // select Template
    public function selectTemplate()
    {
        $templates = Template::where('is_active', true)->get();
        return view('pages.admin.posts.select-template', compact('templates'));
    }


    public function create()
    {
        $categories = Category::all();
        $post = null;

        return view('pages.admin.posts.create', compact('categories', 'post'));
    }

    public function store(PostRequest $request)
    {

        try {
            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null; //need to fix to get main id (true)

            $post = Post::create([
                'title'       => $request->title,
                'sapo_text' => $request->sapo_text,
                'user_id'     => Auth::id(),
                'category_id' => $mainCategoryId,
                'featured_media_id' => $request->input('featured_media_id'),
                'slug'             => $request->slug,
                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
                'status'           => $request->status,
                'visibility'       => $request->visibility,
                'publish_at'       => $request->publish_at,
            ]);

            PostContent::create([
                'post_id'      => $post->id,
                'content_html' => $request->input('content'),
            ]);

            // Assign multiple categories
            $post->categories()->sync($categoryIds);

            return redirect()->route('admin.posts.index')->with('success', '🎉 Bài viết đã được xuất bản!');
        } catch (\Exception $e) {
            return back()->with('error', '😢 Có lỗi xảy ra, vui lòng thử lại.')->withInput();
        }
    }

    public function edit($id)
    {
        $post = Post::with(['content', 'categories'])->findOrFail($id);
        $categories = Category::all();
        $thumbnailMedia = null;

        // keywords arr
        $keywords = $post->seo_keywords
            ? array_map('trim', explode(',', $post->seo_keywords))
            : [];

        if ($post->featured_media_id) {
            $thumbnailMedia = Media::find($post->featured_media_id);
        }

        // get list ID selected categories
        $selectedCategoryIds = $post->categories->pluck('id')->toArray();

        return view('pages.admin.posts.edit', 
        compact('post', 'categories', 'selectedCategoryIds', 'thumbnailMedia', 'keywords'));
    }

    public function update(PostRequest $request, $id)
    {
        try {
            $post = Post::with('content')->findOrFail($id);

            $newContent = $request->input('content');

            // handle category
            $categoryIds = $request->input('category_ids', []);
            $mainCategoryId = $categoryIds[0] ?? null;

            $post->update([
                'title'            => $request->title,
                'sapo_text' => $request->sapo_text,
                'category_id'      => $mainCategoryId,
                'featured_media_id' => $request->input('featured_media_id'),
                'slug'             => $request->slug,
                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
                'status'           => $request->status,
                'visibility'       => $request->visibility,
                'publish_at'       => $request->publish_at,
            ]);

            $postContent = $post->content ?? new PostContent(['post_id' => $post->id]);
            $postContent->content_html = $newContent;
            $postContent->save();

            $post->categories()->sync($categoryIds);

            return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Cập nhật thất bại!')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::with('content')->findOrFail($id);

            // delete if having data
            if ($post->content) {
                $post->content->delete();
            }

            $post->categories()->detach();
            $post->delete();

            return back()->with('success', '🗑️ Bài viết đã được xóa khỏi hệ thống!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Không thể xóa bài viết!');
        }
    }
}
