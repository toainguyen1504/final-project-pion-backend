<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\MediaController;
// use App\Http\Controllers\Api\ConsultationApiController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;

// User
Route::post('/login', [AuthController::class, 'login']);

// categories api
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('media', MediaController::class)->parameters([
        'media' => 'media'
    ]);

    Route::post('/media/{media}/resize', [MediaController::class, 'resize'])->name('media.resize');

    // Route::prefix('consultations')->group(function () {
    // // rate limit 2 requests / 1 minutes
    // Route::middleware('throttle:2,1')->post('/', [ConsultationApiController::class, 'store']);
});

// Route::middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
//     Route::delete('/posts/{post}', [PostController::class, 'destroy']);
// });

// form api - submit for talk show program
Route::post('/form', [FormController::class, 'submit']);
