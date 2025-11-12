<?php

use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\SecurityController;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [SecurityController::class, 'login']);

Route::prefix('/movies')->group(function () {
    Route::middleware(JwtAuthMiddleware::class)->get('/titles', [MovieController::class, 'titles']);
});
