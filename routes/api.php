<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:api', 'isAdmin'])->get('/admin/dashboard', function () {
    return response()->json(['message' => 'Welcome Admin']);
});

Route::middleware(['auth:api', 'isHr'])->get('/client/hr', function () {
    return response()->json(['message' => 'Welcome Client HR']);
});

Route::middleware(['auth:api', 'isEmployee'])->get('/client/employee', function () {
    return response()->json(['message' => 'Welcome Client HR']);
});

// Debug Purpose
Route::middleware('auth:api')->get('/check', function () {
    return response()->json(auth()->user());
});

Route::middleware('auth:api')->get('/whoami', function () {
    return response()->json([
        'user' => auth()->user(),
        'role' => auth()->user()->role
    ]);
});
