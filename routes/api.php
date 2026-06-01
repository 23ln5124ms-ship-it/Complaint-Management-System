<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ResponseController;

Route::middleware('auth:sanctum')->group(function () {
    // Complaints
    Route::apiResource('complaints', ComplaintController::class);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Responses
    Route::get('/complaints/{complaint}/responses', [ResponseController::class, 'index']);
    Route::post('/complaints/{complaint}/responses', [ResponseController::class, 'store']);

    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Public categories endpoint
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
