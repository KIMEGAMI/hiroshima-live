<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LivePostController;

Route::get('/', function () {
    return view('top');
});

Route::get('/api/lives', [LivePostController::class, 'index']);
Route::get('/api/lives/{id}', [LivePostController::class, 'show']);