<?php

use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Web (browser/session) routes only.
| Mobile API routes have been moved to routes/api.php.
|
*/

// Auth
Route::get('/login',    [UserController::class, 'loginPage'])->name('login');
Route::get('/register', [UserController::class, 'registerPage'])->name('register');
Route::post('/login',   [UserController::class, 'login']);
Route::post('/register',[UserController::class, 'register']);
Route::get('/logout',   [UserController::class, 'logout'])->name('logout');

// Dashboard (public — auth check handled inside controller)
Route::get('/', [SurveyController::class, 'dashboard'])->name('dashboard');

// Protected web routes
Route::middleware('auth')->group(function () {
    Route::get('/entry', function () {
        return view('entry', ['route' => 'entry']);
    })->name('entry');

    Route::post('/entry',        [SurveyController::class, 'entry'])->name('entry.store');
    Route::post('/saveCategory', [SurveyController::class, 'saveCategory'])->name('saveCategory');
    Route::get('/history',       [SurveyController::class, 'history'])->name('history');
    Route::get('/export',        [SurveyController::class, 'export'])->name('export');
});