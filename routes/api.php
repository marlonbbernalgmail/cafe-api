<?php

use App\Http\Controllers\Api\Authentication\GetAuthenticatedUserController;
use App\Http\Controllers\Api\Authentication\GetUserPresenceController;
use App\Http\Controllers\Api\Authentication\LoginUserController;
use App\Http\Controllers\Api\Authentication\LogoutUserController;
use App\Http\Controllers\Api\Authentication\RecordAuthenticatedUserPresenceHeartbeatController;
use App\Http\Controllers\Api\Authentication\RegisterUserController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong',
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisterUserController::class);
    Route::post('/login', LoginUserController::class);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', GetAuthenticatedUserController::class);
        Route::post('/presence', RecordAuthenticatedUserPresenceHeartbeatController::class);
        Route::post('/logout', LogoutUserController::class);
    });
});

Route::middleware('auth:sanctum')->get('/user', GetAuthenticatedUserController::class);
Route::middleware('auth:sanctum')->get('/users/{user}/presence', GetUserPresenceController::class);
