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

    // Analytics (custom read-only endpoints)
    Route::prefix('analytics')->group(function () {
        // Users
        Route::get('/users/count', [AnalyticsController::class, 'totalUsers']);
        Route::get('/users/monthly', [AnalyticsController::class, 'newUsersMonthly']);

        // Orders
        Route::get('/orders/count', [AnalyticsController::class, 'totalOrders']);
        Route::get('/orders/revenue', [AnalyticsController::class, 'revenuePerMonth']);

        // Products
        Route::get('/products/top', [AnalyticsController::class, 'topProducts']);

        // Optional: page views (analytics DB)
        Route::get('/page-views', [AnalyticsController::class, 'pageViews']);
    });
});
