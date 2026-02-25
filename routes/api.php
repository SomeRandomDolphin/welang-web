<?php

use App\Http\Controllers\MobileSurveyController;
use App\Http\Controllers\MobileUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Mobile API routes using JWT auth (stateless).
| Base URL: /api/mobile/...
|
*/

Route::prefix('mobile')->group(function () {

    // Public routes — no token needed
    Route::post('/register', [MobileUserController::class, 'register']);
    Route::post('/login',    [MobileUserController::class, 'login']);

    // Protected routes — requires Bearer JWT token
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout',      [MobileUserController::class,  'logout']);
        Route::get('/me',           [MobileUserController::class,  'me']);
        Route::post('/entry',       [MobileSurveyController::class, 'entry']);
        Route::get('/surveys',      [MobileSurveyController::class, 'surveys']);
        Route::get('/categories',   [MobileSurveyController::class, 'categories']);
    });
});