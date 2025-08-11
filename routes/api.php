<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\ConsultationApiController;

Route::get('/posts', [NewsApiController::class, 'index']);
Route::get('/posts/{id}', [NewsApiController::class, 'show']);

Route::prefix('consultations')->group(function () {
    // rate limit 2 requests / 1 minutes
    Route::middleware('throttle:2,1')->post('/', [ConsultationApiController::class, 'store']);
    Route::middleware('auth:sanctum')->get('/my', [ConsultationApiController::class, 'myConsultations']); // Lấy tư vấn cá nhân (nếu cần)
});
