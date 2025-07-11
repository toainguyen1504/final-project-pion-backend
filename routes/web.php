<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CKEditorController;

// Tối ưu route đăng nhập - quên mk sau...
// Route đăng nhập  
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
// Quên mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// =================================================================================
// =================================================================================
// Nhóm dành cho admin và staff
Route::name('admin.')
    ->middleware(['auth', 'role:admin,staff'])
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // News and category
        Route::resource('categories', CategoryController::class);
        Route::resource('news', NewsController::class);
    });


// Nhóm chỉ dành cho admin (super)
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