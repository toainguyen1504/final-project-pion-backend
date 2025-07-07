<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CKEditorController;
use App\Http\Controllers\ClientController;
use App\Models\News;


// CLIENT
Route::get('/', [ClientController::class, 'index'])->name('index');

// Route đăng nhập
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Quên mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// ====================================================
// ====================================================
// GET	/admin/categories	-> index -> admin.categories.index
// GET	/admin/categories/create	-> create ->	admin.categories.create
// POST	/admin/categories	-> store	-> admin.categories.store
// GET	/admin/categories/{id} ->	show ->	admin.categories.show
// GET	/admin/categories/{id}/edit ->	edit ->	admin.categories.edit
// PUT/PATCH	/admin/categories/{id} ->	update ->	admin.categories.update
// DELETE	/admin/categories/{id} ->	destroy ->	admin.categories.destroy

// ADMIN dashboard
Route::get('/admin', function () {
    return view('admin.master');
})->middleware(['auth']);

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('news', NewsController::class);
    });

// Route::prefix('admin')
//     ->name('admin.')
//     ->middleware(['auth', 'can:manage-news'])->group(function () {
//         Route::resource('news', NewsController::class);
//     });


// Route::prefix('admin')
//     ->name('admin.')
//     ->middleware(['auth', 'role:admin,staff'])->group(function () {
//         Route::resource('news', NewsController::class);
//     });


// admin role SuperAdmin - CRUD USER
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin'])
    ->group(function () {
        Route::resource('users', UserController::class);
    });


// CKEditor upload route
Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');


// Nhóm route dành cho admin và staff
// Route::prefix('admin')
//     ->middleware(['auth'])
//     ->group(function () {

//         // CATEGORY
//         // Route::prefix('categories')->name('admin.categories.')->group(function () {
//         //     Route::get('/', [CategoryController::class, 'index'])->name('index');
//         //     Route::post('store', [CategoryController::class, 'store'])->name('store');
//         //     Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('edit');
//         //     Route::put('{id}', [CategoryController::class, 'update'])->name('update');
//         //     Route::delete('{id}', [CategoryController::class, 'destroy'])->name('destroy');
//         // });

//         // NEWS
//         // Route::prefix('news')->name('admin.news.')->group(function () {
//         //     Route::get('/', [NewsController::class, 'index'])->name('index');
//         //     Route::get('create', [NewsController::class, 'create'])->name('create');
//         //     Route::post('store', [NewsController::class, 'store'])->name('store');
//         //     Route::get('{id}/edit', [NewsController::class, 'edit'])->name('edit');
//         //     Route::put('{id}', [NewsController::class, 'update'])->name('update');
//         //     Route::delete('{id}', [NewsController::class, 'destroy'])->name('destroy');
//         // });
//     });


// =====================================
// ADMIN dashboard
// Route::get('/admin', function () {
//     return view('admin.master');
// });

// CATEGORY
// Route::prefix('admin/categories')->group(function () {
//     Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
//     Route::post('store', [CategoryController::class, 'store'])->name('categories.store');
//     Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
//     Route::put('{id}', [CategoryController::class, 'update'])->name('categories.update');
//     Route::delete('{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
// });

// Route::prefix('admin')
//     ->name('admin.')
//     ->middleware(['auth', 'role:admin,staff']) // nếu cần phân quyền
//     ->group(function () {
//         Route::resource('categories', CategoryController::class);
//     });

// NewsController
// Route::prefix('admin/news')->name('admin.news.')->group(function () {
//     Route::get('/', [NewsController::class, 'index'])->name('index');
//     Route::get('create', [NewsController::class, 'create'])->name('create');
//     Route::post('store', [NewsController::class, 'store'])->name('store');

//     Route::get('{id}/edit', [NewsController::class, 'edit'])->name('edit');
//     Route::put('{id}', [NewsController::class, 'update'])->name('update');
//     Route::delete('{id}', [NewsController::class, 'destroy'])->name('destroy');
// });

// User Controller
// Route::prefix('admin/users')->name('users.')->group(function () {
//     Route::get('index', [UserController::class, 'index'])->name('index');
//     Route::get('create', [UserController::class, 'create'])->name('create');
//     Route::post('store', [UserController::class, 'store'])->name('store');
// });
