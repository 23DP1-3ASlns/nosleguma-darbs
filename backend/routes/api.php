<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Public data routes
Route::get('/news', [NewsController::class, 'index']);
Route::get('/schedule', [ScheduleController::class, 'index']);
Route::get('/schedule/groups', [ScheduleController::class, 'groups']);
Route::get('/pages/{slug}', [PageController::class, 'show']);

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Admin-only routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->prefix('admin')->group(function () {
        // News management
        Route::post('/news', [NewsController::class, 'store']);
        Route::post('/news/{news}', [NewsController::class, 'update']); // POST with _method=PUT for file uploads
        Route::put('/news/{news}', [NewsController::class, 'update']);
        Route::delete('/news/{news}', [NewsController::class, 'destroy']);

        // Schedule management
        Route::post('/schedule', [ScheduleController::class, 'store']);
        Route::put('/schedule/{schedule}', [ScheduleController::class, 'update']);
        Route::delete('/schedule/{schedule}', [ScheduleController::class, 'destroy']);

        // Page content management
        Route::put('/pages/{slug}', [PageController::class, 'update']);

        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});
