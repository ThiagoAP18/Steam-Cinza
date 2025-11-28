<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\ProductsController;

Route::get('/', [ProductsController::class, 'index']);
Route::get('/games/create', [ProductsController::class, 'createGame'])->middleware('auth');
Route::post('/games', [ProductsController::class, 'store'])->middleware('auth');
Route::get('/games/{id}', [ProductsController::class, 'show']);
Route::post('/games/buy/{id}', [ProductsController::class, 'buy'])->middleware('auth');
Route::post('/games/rent/{id}', [ProductsController::class, 'rent'])->middleware('auth');
Route::get('/search', [ProductsController::class, 'search']);
Route::get('/addfunds', [ProductsController::class, 'addFunds'])->middleware('auth');
Route::post('/updatefunds', [ProductsController::class, 'updateFunds'])->middleware('auth');
Route::get('/dashboard', [ProductsController::class, 'dashboard'])->middleware('auth');
Route::put('/games/ad/{id}', [ProductsController::class, 'ad'])->middleware('auth');
Route::get('/games/edit/{id}', [ProductsController::class, 'edit'])->middleware('auth');
Route::put('/games/update/{id}', [ProductsController::class, 'update'])->middleware('auth');
Route::post('/games/announce', [ProductsController::class, 'announce'])->middleware('auth')->name('games.announce');
Route::put('/games/rent/return/{id}', [ProductsController::class, 'rent_return'])->middleware('auth');