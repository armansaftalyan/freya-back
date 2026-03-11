<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\GiftCardController;
use App\Http\Controllers\Api\GiftCardOrderController;
use App\Http\Controllers\Api\IdramResultController;
use App\Http\Controllers\Api\SlotController;
use App\Http\Controllers\Api\Admin\GiftCardAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

Route::get('/categories', [CatalogController::class, 'categories']);
Route::get('/services', [CatalogController::class, 'services']);
Route::get('/masters', [CatalogController::class, 'masters']);
Route::get('/masters/{masterKey}', [CatalogController::class, 'master']);
Route::get('/slots', [SlotController::class, 'index']);
Route::post('/slots/combo', [SlotController::class, 'combo']);
Route::post('/appointments', [AppointmentController::class, 'store']);
Route::post('/gift-cards/orders', [GiftCardOrderController::class, 'store']);
Route::post('/gift-cards/orders/webhook', [GiftCardOrderController::class, 'webhook']);
Route::post('/payments/idram/result', IdramResultController::class);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/appointments/my', [AppointmentController::class, 'my']);
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
    Route::get('/gift-cards/my', [GiftCardController::class, 'my']);
    Route::get('/gift-cards/{giftCard}', [GiftCardController::class, 'show']);
    Route::get('/gift-cards/{giftCard}/transactions', [GiftCardController::class, 'transactions']);
    Route::get('/admin/gift-cards/scan/{token}', [GiftCardAdminController::class, 'scan']);
    Route::post('/admin/gift-cards/{giftCard}/redeem', [GiftCardAdminController::class, 'redeem']);
});
