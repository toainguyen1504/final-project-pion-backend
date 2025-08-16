<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Consultation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $totalCategories = Category::count();
        $totalConsultations = Consultation::count();

        $topCategories = Category::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(4)
            ->get();

        // New Posts
        $latestPosts = Post::with('category')
            ->latest()
            ->take(3)
            ->get();

        return view('pages.admin.master', [
            'totalPosts' => $totalPosts,
            'totalCategories' => $totalCategories,
            'totalConsultations' => $totalConsultations,
            'topCategories' => $topCategories,
            'latestPosts' => $latestPosts,
        ]);
    }
}
