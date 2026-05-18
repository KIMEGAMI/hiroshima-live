<?php

use App\Http\Controllers\Api\LivePostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/lives', [LivePostController::class, 'index']);
Route::get('/lives/{id}', [LivePostController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/lives', [LivePostController::class, 'store']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});