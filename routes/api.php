<?php

use App\Modules\Citizens\Presentation\Controllers\CitizenController;
use App\Modules\Citizens\Presentation\Controllers\PasswordResetController;
use App\Modules\Citizens\Presentation\Controllers\EmailVerificationController;
use App\Modules\Requests\Presentation\Controllers\DraftController;
use App\Modules\Requests\Presentation\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    
    Route::prefix('citizens')->group(function () {
        Route::post('/register', [CitizenController::class, 'register']);
        Route::post('/login',    [CitizenController::class, 'login']);
        Route::post('/password-reset',         [PasswordResetController::class, 'sendResetLink']);
        Route::post('/password-reset/confirm', [PasswordResetController::class, 'reset']);
        Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('citizens.verification.verify');
    });

   
    Route::middleware('auth:sanctum-citizens')->group(function () {

       
        Route::prefix('citizens')->group(function () {
            Route::get('/me',      [CitizenController::class, 'me']);
            Route::post('/logout', [CitizenController::class, 'logout']);
            Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
                ->middleware('throttle:6,1');
        });

        
        Route::prefix('portal')->group(function () {
            Route::get('/drafts',         [DraftController::class, 'index']);
            Route::post('/drafts',        [DraftController::class, 'store']);
            Route::get('/drafts/{id}',    [DraftController::class, 'show']);
            Route::patch('/drafts/{id}',  [DraftController::class, 'update']);
            Route::delete('/drafts/{id}', [DraftController::class, 'destroy']);
            Route::post('/attachments',        [AttachmentController::class, 'store']);
            Route::get('/attachments/{id}',    [AttachmentController::class, 'show']);
            Route::delete('/attachments/{id}', [AttachmentController::class, 'destroy']);
        });
    });
});