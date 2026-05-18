<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LivePostController;

Route::get('/', function () {
    return view('top');
});

Route::get('/api/lives', [LivePostController::class, 'index']);
Route::get('/api/lives/{id}', [LivePostController::class, 'show']);

Route::get('/lives/{any}', function () {
    return view('top');
})->where('any', '.*');

Route::get('/lives', function () {
    return view('top');
});

Route::get('/calendar', function () {
    return view('top');
});
Route::get('/api/lives', [LivePostController::class, 'index']);
Route::post('/api/lives', [LivePostController::class, 'store']);
Route::get('/api/lives/{id}', [LivePostController::class, 'show']);