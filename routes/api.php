<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsApiController;

Route::get('/posts', [NewsApiController::class, 'index']);
Route::get('/posts/{id}', [NewsApiController::class, 'show']);
