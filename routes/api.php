<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ConsultationApiController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;

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
    Route::apiResource('media', MediaController::class)->except(['index', 'show']);

    // Consultation (private)
    Route::get('/consultations/my', [ConsultationApiController::class, 'myConsultations']);
});
