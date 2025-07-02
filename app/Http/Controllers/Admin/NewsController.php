<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNewsRequest;
use App\Models\Category;
use App\Models\NewsContent;

class NewsController extends Controller
{
    // public function index(News $news)
    // {
    //     $news = News::latest()->paginate(10);
    //     dd($news); // Dừng lại và hiển thị nội dung của biến $news
    //     return view('admin.news.index', compact('news'));
    // }
    public function index()
    {
        $news = News::with('content')->latest()->paginate(10);
        return view('admin.news.index', compact('news')); // Truyền biến $news đến view
    }

    // public function show($id)
    // {
    //     $news = News::with(['category', 'user', 'content'])->findOrFail($id);
    //     return view('admin.news.show', compact('news'));
    // }

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
                'user_id'     => 1,
                'category_id' => $request->category_id,
            ]);

            // Tạo bản ghi nội dung
            NewsContent::create([
                'news_id' => $news->id,
                'user_id'     => 1,
                'content_html' => $request->content,
            ]);


            return redirect()->route('news.index')->with('success', '🎉 Bài viết đã được xuất bản!');
        } catch (\Exception $e) {
            // return back()
            //     ->with('error', '😢 Có lỗi xảy ra, vui lòng thử lại.')
            //     ->withInput();
            dd($e->getMessage()); // ⬅️ In thông báo lỗi
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
            $news = News::findOrFail($id);
            $news->update([
                'title' => $request->title,
                'category_id' => $request->category_id,
            ]);

            $news->content()->update([
                'content_html' => $request->content,
            ]);

            return redirect()->route('news.index')->with('success', 'Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cập nhật thất bại!')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $news = News::findOrFail($id);
            $news->delete(); // Quan hệ content sẽ bị xóa tự động do cascade

            return back()->with('success', 'Xóa bài viết thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa bài viết!');
        }
    }
}
