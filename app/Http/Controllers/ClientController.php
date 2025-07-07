<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Category;


class ClientController extends Controller
{
    public function index()
    {
        $posts = News::with('category')->latest()->get();
        $categories = Category::all();

        return view('client.master', compact('posts', 'categories'));
    }
}
