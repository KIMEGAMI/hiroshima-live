<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AdminTagController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LivePostController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/lives', [LivePostController::class, 'index']);
Route::get('/lives/{id}', [LivePostController::class, 'show']);

Route::get('/tags', [TagController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/lives', [LivePostController::class, 'store']);

    Route::get('/my/lives', [LivePostController::class, 'myLives']);

    Route::post('/lives/{id}', [LivePostController::class, 'update']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);

        Route::get('/lives', [AdminController::class, 'lives']);
        Route::post('/lives/{id}', [AdminController::class, 'updateLive']);
        Route::delete('/lives/{id}', [AdminController::class, 'deleteLive']);

        Route::get('/tags', [AdminTagController::class, 'index']);
        Route::post('/tags', [AdminTagController::class, 'store']);
        Route::post('/tags/{id}', [AdminTagController::class, 'update']);
        Route::delete('/tags/{id}', [AdminTagController::class, 'destroy']);
    });
});
