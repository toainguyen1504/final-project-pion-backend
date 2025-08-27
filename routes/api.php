<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\MediaApiController;
use App\Http\Controllers\Api\ConsultationApiController;

Route::get('/posts', [PostApiController::class, 'index']);
Route::get('/posts/{id}', [PostApiController::class, 'show']);

Route::apiResource('media', MediaApiController::class);

Route::prefix('consultations')->group(function () {
    // rate limit 2 requests / 1 minutes
    Route::middleware('throttle:2,1')->post('/', [ConsultationApiController::class, 'store']);
    Route::middleware('auth:sanctum')->get('/my', [ConsultationApiController::class, 'myConsultations']); // Lấy tư vấn cá nhân (nếu cần)
});
