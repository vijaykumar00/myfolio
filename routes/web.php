<?php

use App\Helpers\CoreConstants;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Frontend\FrontendController;
use Illuminate\Support\Facades\{Route, Artisan};
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

// Development routes
if (env('APP_ENV') !== 'production') {
    Route::get('command/{cmd}', function ($cmd) {
        try {
            Artisan::call($cmd);
            return response()->json([
                'message' => 'Command successfully executed',
                'payload' => null,
                'status'  => CoreConstants::STATUS_CODE_SUCCESS
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'status'  => CoreConstants::STATUS_CODE_ERROR
            ]);
        }
    });
}

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('system-logs', [LogViewerController::class, 'index']);
    Route::get('/{path?}', [AdminController::class, 'app'])
        ->where('path', '.*')
        ->name('admin.app');
});

// System routes
Route::get('/optimize', [AdminController::class, 'optimize'])
    ->name('optimize')
    ->middleware('auth:admin'); // Consider adding authentication

// Frontend routes
Route::name('frontend.')->group(function () {
    Route::get('/', [FrontendController::class, 'index'])
        ->name('home');
    Route::get('/pixel-tracker', [FrontendController::class, 'pixelTracker'])
        ->name('pixel-tracker');
});
