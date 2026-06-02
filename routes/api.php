<?php
// ============================================================
// routes/api.php
// ============================================================

use App\Http\Controllers\Api\ComplaintApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // Complaints
    Route::get('/complaints',          [ComplaintApiController::class, 'index']);
    Route::post('/complaints',         [ComplaintApiController::class, 'store']);
    Route::get('/complaints/{complaint}',    [ComplaintApiController::class, 'show']);
    Route::put('/complaints/{complaint}',    [ComplaintApiController::class, 'update']);
    Route::patch('/complaints/{complaint}',  [ComplaintApiController::class, 'update']);
    Route::delete('/complaints/{complaint}', [ComplaintApiController::class, 'destroy']);

    // Responses
    Route::get('/complaints/{complaint}/responses',  [ComplaintApiController::class, 'responses']);
    Route::post('/complaints/{complaint}/responses', [ComplaintApiController::class, 'storeResponse']);

    // Categories
    Route::get('/categories', [ComplaintApiController::class, 'categories']);
});
