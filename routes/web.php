<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->name('index.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('/auth/{mode}', [AuthController::class, 'index'])->name('auth');
});
