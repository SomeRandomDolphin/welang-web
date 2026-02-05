<?php
use App\Http\Controllers\MobileSurveyController;
use App\Http\Controllers\MobileUserController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SurveyController::class, 'dashboard'])->name('dashboard');

Route::get('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/register', [UserController::class, 'registerPage'])->name('register');
Route::get('/login', [UserController::class, 'loginPage'])->name('login');
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/entry', function () {
        return view('entry', [
            'route' => 'entry',
        ]);
    })->name('entry');

    Route::post('/entry', [SurveyController::class, 'entry'])->name('entry');
    Route::post('/saveCategory', [SurveyController::class, 'saveCategory'])->name('saveCategory');
    Route::get('/history', [SurveyController::class, 'history'])->name('history');
    Route::get('/export', [SurveyController::class, 'export'])->name('export');
});

Route::prefix('mobile-api')->group(function () {
    Route::post('/register', [MobileUserController::class, 'register']);
    Route::post('/login', [MobileUserController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', [MobileUserController::class, 'logout']);
        Route::get('/me', [MobileUserController::class, 'me']);
        Route::post('/entry', [MobileSurveyController::class, 'entry']);
        Route::get('/home', [MobileSurveyController::class, 'home']);
    });
});
