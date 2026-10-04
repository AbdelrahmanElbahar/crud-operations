<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Each line registers index, store, show, update and destroy under /api/<name>.
Route::apiResource('users', UserController::class);
Route::apiResource('blogs', BlogController::class);
Route::apiResource('posts', PostController::class);
