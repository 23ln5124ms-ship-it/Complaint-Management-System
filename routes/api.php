<?php

use App\Http\Controllers\Api\ComplaintApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ],
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    // Complaints
    Route::get('/complaints',                        [ComplaintApiController::class, 'index']);
    Route::post('/complaints',                       [ComplaintApiController::class, 'store']);
    Route::get('/complaints/{complaint}',            [ComplaintApiController::class, 'show']);
    Route::put('/complaints/{complaint}',            [ComplaintApiController::class, 'update']);
    Route::patch('/complaints/{complaint}',          [ComplaintApiController::class, 'update']);
    Route::delete('/complaints/{complaint}',         [ComplaintApiController::class, 'destroy']);

    // Responses
    Route::get('/complaints/{complaint}/responses',  [ComplaintApiController::class, 'responses']);
    Route::post('/complaints/{complaint}/responses', [ComplaintApiController::class, 'storeResponse']);

    // Categories
    Route::get('/categories', [ComplaintApiController::class, 'categories']);
});