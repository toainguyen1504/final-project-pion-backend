<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Api\EmailVerificationController;

// Dashboard
Route::get('/', function () {
    return view('pages.admin.master');
})->name('admin.dashboard');


// Xác thực email
Route::get(
    '/email/verify/{id}/{hash}',
    [EmailVerificationController::class, 'verify']
)->middleware(['signed'])->name('verification.verify');
