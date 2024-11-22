<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;


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

// Public routes of authtication
Route::controller(LoginRegisterController::class)->group(function() {
    // Route::post('/register', 'register');
    Route::post('/login', 'login');
});

// Protected routes of entry and logout
Route::middleware('auth:sanctum')->group( function () {

    Route::controller(CartController::class)->group(function() {
        Route::post('/cart', 'addToCart');
        Route::post('/checkout', 'checkout');
    });
    Route::post('/paynow', [OrderController::class, 'payNow']);
    Route::get('/orders', [OrderController::class, 'getUserOrders']);
    Route::get('/orders/{orderId}', [OrderController::class, 'getOrderDetails']);
    Route::get('/invoice/{orderId}', [InvoiceController::class, 'generateInvoice']);

    Route::post('/logout', [LoginRegisterController::class, 'logout']);

});
