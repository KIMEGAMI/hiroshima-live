<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LivePostController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/lives', [LivePostController::class, 'index']);
Route::get('/lives/{id}', [LivePostController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/lives', [LivePostController::class, 'store']);

    Route::get('/my/lives', [LivePostController::class, 'myLives']);

    Route::post('/lives/{id}', [LivePostController::class, 'update']);
});