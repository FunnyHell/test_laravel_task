<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned to the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::post('/events/{event}/participate', [EventController::class, 'participate']);
    Route::delete('/events/{event}/withdraw', [EventController::class, 'withdraw']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user/events', [EventController::class, 'getUserEvents']);
    Route::get('/user/{user}', [AuthController::class, 'show']);
});
