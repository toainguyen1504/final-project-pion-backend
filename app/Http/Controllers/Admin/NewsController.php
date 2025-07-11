<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Models\News;
use App\Models\Category;
use App\Models\NewsContent;
use App\Services\ImageService;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('content')->latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.news.create', compact('categories'));
    }

    public function store(StoreNewsRequest $request)
    {
        try {
            $news = News::create([
                'title'       => $request->title,
                'user_id'     => Auth::id(),
                'category_id' => $request->category_id,
            ]);

            NewsContent::create([
                'news_id'      => $news->id,
                'user_id'      => Auth::id(),
                'content_html' => $request->content,
            ]);

            return redirect()->route('admin.news.index')->with('success', '🎉 Bài viết đã được xuất bản!');
        } catch (\Exception $e) {
            return back()->with('error', '😢 Có lỗi xảy ra, vui lòng thử lại.')->withInput();
        }
    }

    public function edit($id)
    {
        $news = News::with('content')->findOrFail($id);
        $categories = Category::all();

        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(StoreNewsRequest $request, $id)
    {
        try {
            $news = News::with('content')->findOrFail($id);

            $oldContent = $news->content->content_html ?? '';
            $newContent = $request->content;

            $oldImages = ImageService::extractImagePaths($oldContent);
            $newImages = ImageService::extractImagePaths($newContent);
            $imagesToDelete = array_diff($oldImages, $newImages);

            foreach ($imagesToDelete as $src) {
                ImageService::deleteIfExists($src);
            }

            $news->update([
                'title'       => $request->title,
                'category_id' => $request->category_id,
            ]);

            if ($news->content) {
                $news->content->content_html = $newContent;
                $news->content->save();
            }

            return redirect()->route('admin.news.index')->with('success', 'Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cập nhật thất bại!')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $news = News::with('content')->findOrFail($id);

            if ($news->content && $news->content->content_html) {
                $images = ImageService::extractImagePaths($news->content->content_html);
                foreach ($images as $src) {
                    ImageService::deleteIfExists($src);
                }
            }

            $news->delete();

            return back()->with('success', '🗑️ Bài viết và ảnh liên quan đã được xóa!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa bài viết!');
        }
    }
}
