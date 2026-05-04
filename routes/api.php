<?php

use App\Http\Controllers\WooCommerceProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('woocommerce')->group(function () {
    Route::post('products', [WooCommerceProductController::class, 'createProduct']);
    Route::put('products/{id}', [WooCommerceProductController::class, 'updateProduct']);
    Route::get('products', [WooCommerceProductController::class, 'getAllProducts']);
    Route::delete('products/{id}', [WooCommerceProductController::class, 'deleteProduct']);
    Route::post('sync', [WooCommerceProductController::class, 'syncProducts']);
});

// Booking API Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('resources', ResourceController::class);
    
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'cancel']);
});
