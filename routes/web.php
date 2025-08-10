<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CKEditorController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Models\News;

// CLIENT
Route::get('/preview/posts', function () {
    $posts = \App\Models\News::with('category')->latest()->get();
    return view('preview.post-list', compact('posts'));
})->name('preview.post.list');

Route::get('/preview/posts/{id}', function ($id) {
    $posts = \App\Models\News::latest()->limit(10)->get();
    $post = News::with('category')->findOrFail($id);
    return view('preview.post-detail', compact('post', 'posts'));
})->name('preview.post.detail');

// Route Login  
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
// Forgot password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// =================================================================================
// =================================================================================
// allow admin and staff
Route::name('admin.')
    ->middleware(['auth', 'role:admin,staff'])
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // News and category
        Route::resource('categories', CategoryController::class);
        Route::resource('news', NewsController::class)->except(['show']);

        // Form Consultation
        Route::resource('consultations', ConsultationController::class)->only(['index', 'destroy']);

        // Select template before create news
        Route::get('news/select-template', [NewsController::class, 'selectTemplate'])->name('news.selectTemplate');
    });


// only admin (super)
Route::name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::resource('users', UserController::class);
    });

// CKEditor upload route
Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])
    ->middleware('auth')
    ->name('ckeditor.upload');

// ====================================================
// ================Example NOTE when use Route::resource====================================
// GET	/admin/categories	-> index -> admin.categories.index
// GET	/admin/categories/create	-> create ->	admin.categories.create
// POST	/admin/categories	-> store	-> admin.categories.store
// GET	/admin/categories/{id} ->	show ->	admin.categories.show
// GET	/admin/categories/{id}/edit ->	edit ->	admin.categories.edit
// PUT/PATCH	/admin/categories/{id} ->	update ->	admin.categories.update
// DELETE	/admin/categories/{id} ->	destroy ->	admin.categories.destroy