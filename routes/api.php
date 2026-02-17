<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\SlotController;
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
Route::get('/branches', [CatalogController::class, 'branches']);
Route::get('/slots', [SlotController::class, 'index']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/my', [AppointmentController::class, 'my']);
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
});
