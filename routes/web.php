<?php

use App\Http\Controllers\Web\PixController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use App\Livewire\Web\PixCreator;
use App\Livewire\Web\PixList;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->name('index.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('/auth/{mode}', [AuthController::class, 'index'])->name('auth');
});

Route::middleware('auth')->group(function () {
    Route::get('/pix/create', PixCreator::class)->name('pix.create');
    Route::get('/pix/lista', PixList::class)->name('pix.list');
});

// Esta rota DEVE vir por último para não dar problemas nas rotas com proteção
Route::get('/pix/{token}', [PixController::class, 'show'])->name('pix.show');
Route::post('/pix/{token}', [PixController::class, 'confirm'])->name('pix.confirm');
