<?php

use App\Modules\Citizens\Presentation\Controllers\CitizenController;
use App\Modules\Requests\Presentation\Controllers\DraftController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // === Rutas públicas de ciudadanos ===
    Route::prefix('citizens')->group(function () {
        Route::post('/register', [CitizenController::class, 'register']);
        Route::post('/login',    [CitizenController::class, 'login']);
    });

    // === Rutas protegidas con token ===
    Route::middleware('auth:sanctum-citizens')->group(function () {

        // Ciudadano autenticado
        Route::prefix('citizens')->group(function () {
            Route::get('/me',      [CitizenController::class, 'me']);
            Route::post('/logout', [CitizenController::class, 'logout']);
        });

        // Borradores (todos requieren auth)
        Route::prefix('portal')->group(function () {
            Route::get('/drafts',         [DraftController::class, 'index']);
            Route::post('/drafts',        [DraftController::class, 'store']);
            Route::get('/drafts/{id}',    [DraftController::class, 'show']);
            Route::patch('/drafts/{id}',  [DraftController::class, 'update']);
            Route::delete('/drafts/{id}', [DraftController::class, 'destroy']);
        });
    });
});