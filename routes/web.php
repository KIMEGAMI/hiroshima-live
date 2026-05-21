<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('spa');
})->name('login');

Route::get('/reset-password', function () {
    return view('spa');
})->name('password.reset');

Route::get('/{any}', function () {
    return view('spa');
})->where('any', '.*');