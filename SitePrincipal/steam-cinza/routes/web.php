<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\ProductsController;

Route::get('/', [ProductsController::class, 'index']);
Route::get('/games/{id}', [ProductsController::class, 'show']);
Route::get('/search', [ProductsController::class, 'search']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
