<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TicketController;




Route::post('register', [UserAuthController::class, 'register']);
Route::post('login', [UserAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', function (Request $request) {
    return $request->user();
    });

    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('events', [EventController::class, 'Events']);
    Route::get('events/{id}', [EventController::class, 'getEventById']);



    Route::middleware('role:organizer')->group(function () {
        Route::post('events', [EventController::class, 'store']);
        Route::put('events/{event}', [EventController::class, 'update']);
        Route::delete('events/{event}', [EventController::class, 'destroy']);
        Route::post('tickets', [TicketController::class, 'store']);
    });

    // Tickets (Organizer only)
    Route::middleware('role:organizer')->group(function () {
        Route::post('events/{event_id}/tickets', [TicketController::class, 'store']);
        Route::put('tickets/{id}', [TicketController::class, 'update']);
        Route::delete('tickets/{id}', [TicketController::class, 'delete']);
    });

    // Customer routes
    Route::middleware('role:customer')->group(function () {
        Route::post('tickets/{id}/bookings', [BookingController::class, 'store'])->middleware('prevent.double.booking');
        Route::get('bookings ', [BookingController::class, 'index']);
        Route::put('bookings/{id}/cancel', [BookingController::class, 'cancel']);

    });

});

