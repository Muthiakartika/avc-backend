<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api', 'verified'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:api', 'verified', 'admin'])->get('/admin/dashboard', function () {
    return response()->json(['message' => 'Welcome Admin']);
});

Route::middleware(['auth:api', 'verified', 'hr'])->get('/client/hr', function () {
    return response()->json(['message' => 'Welcome HR']);
});

Route::middleware(['auth:api', 'verified', 'user'])->get('/client/employee', function () {
    return response()->json(['message' => 'Welcome User']);
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

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    // Cek apakah hash cocok
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid or expired verification link.'], 403);
    }

    // Cek apakah sudah verifikasi
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Already verified'], 200);
    }

    // Tandai sebagai terverifikasi
    $user->markEmailAsVerified();

    // Buat token JWT
    $token = JWTAuth::fromUser($user);

    return response()->json([
        'message' => 'Email verified successfully.',
        'token' => $token,
    ], 200);

})->middleware(['signed'])->name('verification.verify');
