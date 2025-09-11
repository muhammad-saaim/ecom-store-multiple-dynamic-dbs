<?php

use Illuminate\Http\Request;
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
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Example test route
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working 🚀']);
});

// REST APIs
Route::apiResource('users', UserController::class);
Route::apiResource('profiles', ProfileController::class);
Route::apiResource('orders', OrderController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('analytics', AnalyticsController::class);
