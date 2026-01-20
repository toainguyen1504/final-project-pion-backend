<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ConsultationApiController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// -----------------------------
// 🔓 Public routes (Không cần token)
// -----------------------------

// Auth
Route::post('/login', [AuthController::class, 'login']);

// Public content
// category
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/stats', [CategoryController::class, 'stats']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// post
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/stats', [PostController::class, 'stats']);
Route::get('/posts/{id}', [PostController::class, 'show']);



// Public media (cho FE hiển thị ảnh)
Route::get('/media', [MediaController::class, 'index']);
Route::get('/media/{id}', [MediaController::class, 'show']);

// Public forms
// form tư vấn
Route::prefix('consultations')->group(function () {
    Route::middleware('throttle:2,1')->post('/', [ConsultationApiController::class, 'store']);
});

// Form đăng ký
Route::post('/form', [FormController::class, 'submit']);

// -----------------------------
// 🔒 Protected routes (admin, cần token)
// -----------------------------
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Categories
    Route::post('/categories/bulk-destroy', [CategoryController::class, 'bulkDestroy']);
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

    // Posts
    Route::post('/posts/bulk-destroy', [PostController::class, 'bulkDestroy']);
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);

    // Media
    Route::post('/media/{media}/resize', [MediaController::class, 'resize']);
    Route::apiResource('media', MediaController::class)
        ->parameters(['media' => 'media']) // ép Laravel dùng {media} thay vì {medium}
        ->except(['index', 'show']);

    // Consultation (private)
    Route::get('/consultations', [ConsultationApiController::class, 'index']);
    Route::get('/consultations/export', [ConsultationApiController::class, 'export']);
    Route::get('/consultations/my', [ConsultationApiController::class, 'myConsultations']); //don't use
});

// chỉ admin/super_admin mới được CRUD users
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // CRUD
    Route::apiResource('users', UserController::class)
        ->middleware('role:admin|super_admin');

    // Reset password nhanh
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->middleware('role:admin|super_admin');

    // Get list roles (staff, admin, super_admin mới được phép) 
    Route::get('/roles', [UserController::class, 'getRoles'])->middleware('role:staff|admin|super_admin');

    // Get roles trừ super_admin và guest 
    Route::get('/roles/available', [UserController::class, 'rolesAvailable']) ->middleware('role:admin|super_admin');
});
