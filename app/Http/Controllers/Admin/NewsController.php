<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use App\Models\News;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Models\Category;
use App\Models\NewsContent;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('content')->latest()->paginate(10);
        return view('admin.news.index', compact('news')); // Truyền biến $news đến view
    }

    public function create()
    {
        $categories = Category::all(); // Lấy danh sách danh mục
        return view('admin.news.create', compact('categories'));
    }

    public function store(StoreNewsRequest $request)
    {
        try {
            // Tạo bản ghi trong bảng news
            $news = News::create([
                'title'       => $request->title,
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
            ]);

            // Tạo bản ghi nội dung
            NewsContent::create([
                'news_id' => $news->id,
                'user_id' => Auth::id(),
                'content_html' => $request->content,
            ]);


            return redirect()->route('admin.news.index')->with('success', '🎉 Bài viết đã được xuất bản!');
        } catch (\Exception $e) {
            return back()
                ->with('error', '😢 Có lỗi xảy ra, vui lòng thử lại.')
                ->withInput();
            // dd($e->getMessage()); // ⬅️ In thông báo lỗi
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

            // ✅ Tìm ảnh trong nội dung cũ
            $oldImages = $this->extractImagePaths($oldContent);

            // ✅ Tìm ảnh trong nội dung mới
            $newImages = $this->extractImagePaths($newContent);

            // ✅ Tìm ảnh đã bị loại bỏ
            $imagesToDelete = array_diff($oldImages, $newImages);

            foreach ($imagesToDelete as $src) {
                $path = public_path(parse_url($src, PHP_URL_PATH));
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            // ✅ Cập nhật bài viết
            $news->update([
                'title' => $request->title,
                'category_id' => $request->category_id,
            ]);

            $news->content()->update([
                'content_html' => $newContent,
            ]);

            return redirect()->route('admin.news.index')->with('success', 'Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cập nhật thất bại!')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $news = News::with('content')->findOrFail($id);

            // ✅ Nếu có nội dung HTML, tìm và xóa ảnh
            if ($news->content && $news->content->content_html) {
                $html = $news->content->content_html;

                // Dùng DOMDocument để phân tích HTML
                libxml_use_internal_errors(true);
                $dom = new \DOMDocument();
                $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                $images = $dom->getElementsByTagName('img');

                foreach ($images as $img) {
                    $src = $img->getAttribute('src');

                    // Nếu ảnh nằm trong thư mục uploads
                    if (strpos($src, '/uploads/') !== false) {
                        $path = public_path(parse_url($src, PHP_URL_PATH));
                        if (File::exists($path)) {
                            File::delete($path);
                        }
                    }
                }
            }

            // Xóa bài viết (cascade sẽ xóa content nếu đã thiết lập)
            $news->delete();

            return back()->with('success', '🗑️ Bài viết và ảnh liên quan đã được xóa!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa bài viết!');
        }
    }

    private function extractImagePaths(?string $html): array
    {
        $paths = [];

        if (!$html) return $paths;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            if (strpos($src, '/uploads/') !== false) {
                $paths[] = $src;
            }
        }

        return $paths;
    }
}
