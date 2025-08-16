<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CKEditorController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Models\Post;
use App\Models\Category;

// CLIENT
Route::get('/client', function () {
    $posts = Post::with('category')->latest()->get();
    $categories = Category::all();

    return view('pages.client.master', compact('posts', 'categories'));
})->name('client');

Route::get('/client/posts', function () {
    $posts = Post::with('category')->latest()->get();
    return view('pages.client.posts.post-list', compact('posts'));
})->name('client.post.list');

Route::get('/client/posts/{id}', function ($id) {
    $posts = Post::latest()->limit(10)->get();
    $post = Post::with('category')->findOrFail($id);
    return view('pages.client.posts.post-detail', compact('post', 'posts'));
})->name('client.post.detail');

// Route Login  
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
// Forgot password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// =================================================================================
// =================================================================================
// Dashboard
Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('admin.dashboard');

// admin + staff + staffads: CRUD posts, categories, consultations (access all - users)
Route::middleware(['auth', 'role:admin,staff,staffads'])
    ->name('admin.')
    ->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('consultations', ConsultationController::class)->only(['index']);
        Route::get('posts/select-template', [PostController::class, 'selectTemplate'])->name('posts.selectTemplate');
    });

// staffads: only access CRUD posts, categories, consultations
// Route::middleware(['auth', 'role:staffads'])
//     ->prefix('admin')
//     ->name('admin.staffads.')
//     ->group(function () {
//         // Route::resource('categories', CategoryController::class)->only(['index']);
//         // Route::resource('posts', PostController::class)->only(['index']);
//         // Route::resource('consultations', ConsultationController::class)->only(['index']);
//         Route::resource('categories', CategoryController::class);
//         Route::resource('posts', PostController::class)->except(['show']);
//         Route::resource('consultations', ConsultationController::class)->only(['index']);
//     });

// ONLY admin: CRUD users
Route::middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);
    });

// ✅ CKEditor upload
Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])
    ->middleware('auth')
    ->name('ckeditor.upload');
