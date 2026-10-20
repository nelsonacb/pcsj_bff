<?php

// use Illuminate\Http\Request;
use App\Modules\Citizens\Presentation\Controllers\CitizenController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('citizens')->group(function () {
        // Rutas públicas
        Route::post('/register', [CitizenController::class, 'register']);
        Route::post('/login',    [CitizenController::class, 'login']);

        // Rutas protegidas: requieren token válido del guard sanctum-citizens
        Route::middleware('auth:sanctum-citizens')->group(function () {
            Route::get('/me',     [CitizenController::class, 'me']);
            Route::post('/logout',[CitizenController::class, 'logout']);
        });
    });
});