<!-- 

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', [HealthController::class, 'index']);

    Route::prefix('citizens')->group(function () {
        Route::post('/register', [CitizenAuthController::class, 'register'])
            ->middleware('throttle:3,60');
        Route::post('/login', [CitizenAuthController::class, 'login'])
            ->middleware('throttle:10,60');
        Route::post('/password-reset', [CitizenAuthController::class, 'requestReset'])
            ->middleware('throttle:3,60');
        Route::post('/password-reset/confirm', [CitizenAuthController::class, 'confirmReset']);

        Route::middleware('auth:sanctum-citizens')->group(function () {
            Route::get('/me', [CitizenAuthController::class, 'me']);
            Route::post('/logout', [CitizenAuthController::class, 'logout']);
        });
    });
}); -->