<?php

namespace App\Http\Controllers\Admin;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Category;
use App\Models\Template;
use App\Models\PostContent;
use App\Services\ImageService;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('content')->latest()->paginate(10);
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
        return view('pages.admin.posts.create', compact('categories'));
    }

    public function store(PostRequest $request)
    {
        try {
            $post = Post::create([
                'title'       => $request->title,
                'user_id'     => Auth::id(),
                'category_id' => null, // or category_ids[0] - main category
                'slug' => $request->slug ?? null,
                'seo_title'   => $request->seo_title,
                'seo_description' => $request->seo_description,
                'seo_keywords'    => implode(',', $request->input('seo_keywords', [])),
            ]);

            PostContent::create([
                'post_id'      => $post->id,
                'content_html' => $request->input('content'),
            ]);

            // Assign multiple categories
            $categoryIds = $request->input('category_ids', []);
            $post->categories()->sync($categoryIds);

            return redirect()->route('admin.posts.index')->with('success', '🎉 Bài viết đã được xuất bản!');
        } catch (\Exception $e) {
            return back()->with('error', '😢 Có lỗi xảy ra, vui lòng thử lại.')->withInput();
        }
    }

    public function edit($id)
    {
        $posts = Post::with('content')->findOrFail($id);
        $categories = Category::all();

        return view('pages.admin.posts.edit', compact('posts', 'categories'));
    }

    public function update(PostRequest $request, $id)
    {
        try {
            $posts = Post::with('content')->findOrFail($id);

            $oldContent = $posts->content->content_html ?? '';
            $newContent = $request->input('content');

            $oldImages = ImageService::extractImagePaths($oldContent);
            $newImages = ImageService::extractImagePaths($newContent);
            $imagesToDelete = array_diff($oldImages, $newImages);

            foreach ($imagesToDelete as $src) {
                ImageService::deleteIfExists($src);
            }

            $posts->update([
                'title'       => $request->title,
                'category_id' => $request->category_id,
            ]);

            $postsContent = $posts->content ?? new PostContent(['post_id' => $posts->id]);
            $postsContent->content_html = $newContent;
            $postsContent->save();

            return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cập nhật thất bại!')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $posts = Post::with('content')->findOrFail($id);

            if ($posts->content && $posts->content->content_html) {
                $images = ImageService::extractImagePaths($posts->content->content_html);
                foreach ($images as $src) {
                    ImageService::deleteIfExists($src);
                }
            }

            $posts->delete();

            return back()->with('success', '🗑️ Bài viết và ảnh liên quan đã được xóa!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa bài viết!');
        }
    }
}
