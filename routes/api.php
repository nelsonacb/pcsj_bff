<?php

// use Illuminate\Http\Request;
use App\Modules\Citizens\Presentation\Controllers\CitizenController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('citizens')->group(function () {
        Route::post('/register', [CitizenController::class, 'register']);
    });
});