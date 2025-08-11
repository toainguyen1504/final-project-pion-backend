<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Category;
use App\Models\Consultation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Tổng số bài viết và danh mục
        $totalNews = News::count();
        $totalCategories = Category::count();
        $totalConsultations = Consultation::count();

        // Danh mục nổi bật (có nhiều bài viết nhất)
        $topCategories = Category::withCount('news')
            ->orderByDesc('news_count')
            ->take(4)
            ->get();

        // Tin tức mới nhất
        $latestNews = News::with('category')
            ->latest()
            ->take(3)
            ->get();

        return view('admin.master', [
            'totalNews' => $totalNews,
            'totalCategories' => $totalCategories,
            'totalConsultations' => $totalConsultations,
            'topCategories' => $topCategories,
            'latestNews' => $latestNews,
        ]);
    }
}
