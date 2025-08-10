<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\ConsultationApiController;

Route::get('/posts', [NewsApiController::class, 'index']);
Route::get('/posts/{id}', [NewsApiController::class, 'show']);

Route::prefix('consultations')->group(function () {
    Route::post('/', [ConsultationApiController::class, 'store']); // Gửi tư vấn từ frontend
    Route::middleware('auth:sanctum')->get('/my', [ConsultationApiController::class, 'myConsultations']); // Lấy tư vấn cá nhân (nếu cần)
});
