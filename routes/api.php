<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Routes for TicketController
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
Route::post('/tickets/purchase', [TicketController::class, 'purchase']);
Route::post('/tickets/{ticket}/cancel', [TicketController::class, 'cancel']);

// Routes for ReportController
Route::get('/reports/revenue', [ReportController::class, 'revenue']);
Route::get('/reports/tickets-sold', [ReportController::class, 'ticketsSold']);
