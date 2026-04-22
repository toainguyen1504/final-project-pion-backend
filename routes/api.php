<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ConsultationApiController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\FlashcardController;
use App\Http\Controllers\Api\LessonProgressController;
use App\Http\Controllers\Api\LessonNoteController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\MomoIpnController;

// -----------------------------
// Common Public routes
// -----------------------------

// Auth
Route::post('/cms/login', [AuthController::class, 'loginCms']); // CMS login
Route::post('/client/login', [AuthController::class, 'loginClient']); // Client login
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']); // logout chung

// category
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Public media (cho FE hiển thị ảnh)
// tối ưu: chỉ lấy danh sách media công khai và đang dùng cho post (Client site)
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
//  START - Public routes cho Client Site (Frontend)
// -----------------------------
Route::prefix('client')->group(function () {
    // Posts
    Route::get('/posts', [PostController::class, 'indexClient']);
    Route::get('/posts/{id}', [PostController::class, 'showClient']);

    // Flashcards
});

Route::prefix('client')->middleware('auth:sanctum')->group(function () {

    // Courses
    Route::get('/courses', [CourseController::class, 'indexClient']);
    Route::get('/courses/{slug}', [CourseController::class, 'showClient']);
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
    Route::get('/courses/{slug}/learning', [CourseController::class, 'learningDetail']);

    // Progress
    Route::post('/lesson-progress', [LessonProgressController::class, 'update']);
    Route::get('/lesson-progress/{lessonId}', [LessonProgressController::class, 'getLessonProgress']);
    Route::get('/me/learning-courses', [LessonProgressController::class, 'myLearningCourses']);
    Route::get('/me/current-learning', [LessonProgressController::class, 'currentLearning']);

    // flashcard
    Route::get('/lessons/{lessonId}/flashcards', [FlashcardController::class, 'getByLessonClient']);

    // Note
    Route::get('/courses/{courseId}/notes', [LessonNoteController::class, 'getByCourse']);
    Route::get('/lessons/{lessonId}/notes', [LessonNoteController::class, 'index']);
    Route::post('/notes', [LessonNoteController::class, 'store']);
    Route::put('/notes/{id}', [LessonNoteController::class, 'update']);
    Route::delete('/notes/{id}', [LessonNoteController::class, 'destroy']);

    // Payment
    Route::post('/payments/momo/create', [PaymentController::class, 'createMomoPayment']);
    Route::get('/orders/{orderNumber}/status', [PaymentController::class, 'getOrderStatus']);
    Route::post('/payments/mock-success', [PaymentController::class, 'mockSuccess']);
});

Route::post('/client/payments/momo/ipn', MomoIpnController::class);

// -----------------------------
//  END - Public routes cho Client Site(Frontend)
// -----------------------------

// -----------------------------
// ** Protected routes - chỉ role: admin, staff, staffads, super_admin mới được truy cập **
// -----------------------------
// categories, posts, media, consultations
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // CRUD categories, posts, media, consultations
    Route::middleware('role:admin|staff|staffads|super_admin')->group(function () {

        // Categories
        Route::get('/categories/stats', [CategoryController::class, 'stats']);
        Route::post('/categories/bulk-destroy', [CategoryController::class, 'bulkDestroy']);
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

        // Posts
        Route::get('/posts/stats', [PostController::class, 'stats']); // phải đặt trước route  apiResource để không bị ghi đè với detail post
        Route::post('/posts/bulk-destroy', [PostController::class, 'bulkDestroy']);
        Route::apiResource('posts', PostController::class);

        // Media
        Route::post('/media/{media}/resize', [MediaController::class, 'resize']);
        Route::apiResource('media', MediaController::class)
            ->parameters(['media' => 'media']) // ép Laravel dùng {media} thay vì {medium}
            ->except(['index', 'show']);

        // Consultation (private)
        Route::get('/consultations/stats', [ConsultationApiController::class, 'stats']);
        Route::get('/consultations', [ConsultationApiController::class, 'index']);
        Route::get('/consultations/export', [ConsultationApiController::class, 'export']);
        Route::get('/consultations/my', [ConsultationApiController::class, 'myConsultations']); //don't use
    });

    // CRUD users, roles
    Route::middleware('role:admin|super_admin')->group(function () {

        Route::get('/users/stats', [UserController::class, 'stats']); // phải đặt trước route  apiResource để không bị ghi đè với detail post

        Route::apiResource('users', UserController::class);

        // Reset password nhanh
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);

        // Get list roles (admin, super_admin mới được phép) 
        Route::get('/roles', [UserController::class, 'getRoles']);

        // Get roles trừ super_admin và guest 
        Route::get('/roles/available', [UserController::class, 'rolesAvailable']);
    });

    // CRUD programs, courses, lessons, lesson notes,... (E-learning)
    Route::middleware('role:admin|super_admin|teacher')->group(function () {
        // Programs
        Route::post('/programs/bulk-destroy', [ProgramController::class, 'bulkDestroy']);
        Route::apiResource('programs', ProgramController::class);

        // Courses
        Route::post('/courses/bulk-destroy', [CourseController::class, 'bulkDestroy']);
        Route::apiResource('courses', CourseController::class);

        // Lessons 
        Route::get('/courses/{courseId}/lessons', [LessonController::class, 'getByCourse']);
        Route::post('/lessons/bulk-destroy', [LessonController::class, 'bulkDestroy']);
        Route::apiResource('lessons', LessonController::class);

        // Flashcard
        Route::get('/lessons/{lessonId}/flashcards', [FlashcardController::class, 'getByLesson']); // optional
        Route::post('/flashcards/bulk', [FlashcardController::class, 'bulkStore']);
        Route::post('/flashcards/bulk-destroy', [FlashcardController::class, 'bulkDestroy']);
        Route::apiResource('flashcards', FlashcardController::class);
    });
});
