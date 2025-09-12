<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AnalyticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes are registered here. They are automatically assigned
| the "api" middleware group.
|
*/

// ✅ Health-check route
Route::get('/ping', fn() => response()->json(['message' => 'API is working 🚀']));

// ✅ Grouped API routes
Route::prefix('v1')->group(function () {

    // Users
    Route::apiResource('users', UserController::class);

    // Profiles nested under users (one-to-one)
    Route::get('users/{user}/profile', [ProfileController::class, 'show']);
    Route::post('users/{user}/profile', [ProfileController::class, 'store']);
    Route::put('users/{user}/profile', [ProfileController::class, 'update']);
    Route::delete('users/{user}/profile', [ProfileController::class, 'destroy']);

    // Orders (linked to users: one-to-many)
    Route::apiResource('orders', OrderController::class);

    // Products (linked with orders: many-to-many)
    Route::apiResource('products', ProductController::class);

    // Analytics (separate DB: page views, etc.)
    Route::apiResource('analytics', AnalyticsController::class);
});
