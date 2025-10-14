<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', function () {
    return view('pages.admin.master');
})->name('admin.dashboard');