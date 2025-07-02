<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CKEditorController;
use App\Http\Controllers\ClientController;
use App\Models\News;


// CLIENT
Route::get('/', [ClientController::class, 'index'])->name('index');



// ====================================================
// ====================================================
// ====================================================
// ADMIN
Route::get('/admin', function () {
    return view('admin.master');
});

// NewsController
Route::prefix('admin/news')->name('news.')->group(function () {
    Route::get('index', [NewsController::class, 'index'])->name('index');
    Route::get('create', [NewsController::class, 'create'])->name('create');
    Route::post('store', [NewsController::class, 'store'])->name('store');

    Route::get('{id}/edit', [NewsController::class, 'edit'])->name('edit');
    Route::put('{id}', [NewsController::class, 'update'])->name('update');
    Route::delete('{id}', [NewsController::class, 'destroy'])->name('destroy');

    // Nếu sau này thêm: Route::post('store', ...) cho store()
});

// Route::get('/admin/news/{id}', [NewsController::class, 'show'])->name('news.show');

// CATEGORY
Route::prefix('admin/categories')->group(function () {
    Route::get('index', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});


// CKEditor upload route
Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');

// Route::resource('news', NewsController::class);
// Route::resource('news', NewsController::class);

// Route::get('/admin/news/index', function () {
//     $news = News::with('user')->latest()->paginate(10); 
//     return view('admin.news.index', compact('news'));
// });

// Route::middleware(['auth'])->group(function () {
//     Route::resource('news', NewsController::class);
// });
